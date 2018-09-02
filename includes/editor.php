<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Editor
{
	public function __construct()
	{

	}

	/**
	 * Is Screen
	 * 
	 * Returns true if current screen is editor screen.
	 *
	 * @return boolean
	 */
	public function is_screen()
	{
		// Checks if admin area.

		if ( ! is_admin() ) 
		{
			return false;
		}

		global $pagenow, $typenow;

		// Checks if current page is post 'add' or 'edit' page

		if ( $pagenow != 'post.php' && $pagenow != 'post-new.php' ) 
		{
			return false;
		}

		// Checks if post type supports page builder

		return post_type_supports( $typenow, PB_POST_TYPE_FEATURE );
	}

	/**
	 * Add Meta Boxes
	 * 
	 * Adds editor meta box.
	 */
	public function add_meta_boxes()
	{
		if ( ! $this->is_screen() ) 
		{
			return;
		}

		add_meta_box( 'pb-editor-meta-box', __( 'Page Builder' ), array( $this, 'render' ), null, 'advanced', 'high' );
	}

	/**
	 * Render
	 * 
	 * Renders editor
	 * 
	 * @param $post int|WP_Post The post object or id.
	 */
	public function render( $post )
	{
		$models = pb()->models->get_post_models( $post );

		?>

		<div id="pb-editor" class="pb-editor">

			<?php wp_nonce_field( 'editor', PB_NONCE_NAME ); ?>

			<p class="pb-show-if-debug">
				<textarea class="pb-source large-text code" name="pb_models" rows="10"><?php echo json_encode( $models ); ?></textarea>
			</p>

			<div class="pb-available-widgets">
				<?php pb()->widgets->list_editor_widgets(); ?>
			</div>

			<div class="pb-main-widget-container"></div>

			<div class="pb-editor-footer">
				<button type="button" class="pb-add-widget-control"><?php esc_html_e( 'Add Widget' ); ?></button>
			</div>

		</div>

		<?php

		/**
		 * JavaScript
		 * -----------------------------------------------------------
		 */

		// Editor options
		
		$options = array
		(
			'nonce'          => wp_create_nonce( 'editor', PB_NONCE_NAME ),
			'nonceName'      => PB_NONCE_NAME,
			'widgetDefaults' => array()
		);

		// Sets widget defaults

		$widgets = pb()->widgets->get_widgets();

		foreach ( $widgets as $widget ) 
		{
			$options['widgetDefaults'][ $widget->id ] = $widget->get_defaults();
		}

		//

		?>

		<script type="text/javascript">
			
			jQuery( document ).ready( function()
			{
				pb.init( '#pb-editor', <?php echo json_encode( $options ); ?> );
			});

		</script>

		<?php
	}

	public function save_post( $post_id, $update )
	{
		/*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
 
        // Check if our nonce is set.
        if ( ! isset( $_POST[ PB_NONCE_NAME ] ) ) 
        {
            return $post_id;
        }
 
        $nonce = $_POST[ PB_NONCE_NAME ];
 
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'editor' ) ) 
        {
            return $post_id;
        }
 
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        {
            return $post_id;
        }
 
        // Check the user's permissions.
        if ( $_POST['post_type'] == 'page' ) 
        {
            if ( ! current_user_can( 'edit_page', $post_id ) ) 
            {
                return $post_id;
            }
        } 

        else 
        {
            if ( ! current_user_can( 'edit_post', $post_id ) ) 
            {
                return $post_id;
            }
        }
 
        // OK, it's safe for us to save the data now

        $models = isset( $_POST['pb_models'] ) ? $_POST['pb_models'] : '';
        $models = stripcslashes( $models );
        $models = json_decode( $models, true );

        if ( json_last_error() != JSON_ERROR_NONE ) 
        {
        	error_log( sprintf( 'Unable to save data: %s', json_last_error() ) );

        	$models = array();
        }

        pb()->models->save_post_models( $models, $post_id );
	}

	public function widget_picker()
	{
		// Checks if ajax

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) 
		{
			return;
		}

		// Checks nonce and referer

		check_admin_referer( 'editor', PB_NONCE_NAME );

		//

		$parent = isset( $_POST['parent'] ) ? $_POST['parent'] : '';

		$available = pb()->widgets->get_widgets();
		$available = array_combine( array_keys( $available ), array_fill( 0, count( $available ), true ) );

		switch ( $parent ) 
		{
			case '' :

				$available['column'] = false;

				break;

			case 'row':

				$available = array( 'column' => true );

				break;

			case 'column':

				$available['column'] = false;

				break;

			default :

				$available = array();
		}

		$available = array_filter( $available );

		?>

		<div id="pb-widget-picker">

			<h1><?php _e( 'Available Widgets' ); ?></h1>

			<?php 

				if ( ! $available ) 
				{
					if ( $parent ) 
					{
						$widget = pb()->widgets->get_widget( $parent );

						pb_notice( sprintf( __( '%s widget cannot contain other widgets.' ), $widget->title ) );
					}

					else
					{
						pb_notice( __( 'No widgets available.' ) );
					}
				}

				else
				{
					pb()->widgets->list_editor_widgets( array
					(
						'before'        => '<div class="pb-row">',
						'before_widget' => '<div class="pb-col-md-4">',
						'after_widget'  => '</div>',
						'after'         => '</div>',
						'include'       => array_keys( $available )
					));
				}

			?>

		</div>

		<?php

		wp_die();
	}

	public function widget_settings()
	{
		// Checks if ajax

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) 
		{
			return;
		}

		// Checks nonce and referer

		check_admin_referer( 'editor', PB_NONCE_NAME );

		//

		$widget_id = isset( $_POST['widget'] ) ? $_POST['widget'] : '';

		$widget = pb()->widgets->get_widget( $widget_id );

		$widget->form();

		wp_die();
	}

	public function widget_preview()
	{
		// Checks if ajax

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) 
		{
			return;
		}

		// Checks nonce and referer

		check_admin_referer( 'editor', PB_NONCE_NAME );

		//

		$models = isset( $_POST['models'] ) ? (array) $_POST['models'] : array();

		$preview = array();

		foreach ( $models as $model ) 
		{
			pb_stripcslashes( $model );
			
			// Makes sure all properties are set

			$model = pb()->models->create_model( $model );

			// Gets widget

			$widget = pb()->widgets->get_widget( $model['type'] );

			if ( ! $widget ) 
			{
				continue;
			}

			// Gets preview

			ob_start();

			$widget->preview( $model['data'] );

			$preview[ $model['id'] ] = ob_get_clean();
		}

		wp_send_json( $preview );
	}

	public function enqueue_scripts()
	{
		if ( ! $this->is_screen() ) 
		{
			return;
		}

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_script( 'wp-util' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		// Select2
		wp_enqueue_style( 'select2', plugins_url( 'vendor/select2/dist/css/select2.min.css', PB_FILE ), null, '4.0.5' );
		wp_enqueue_script( 'select2', plugins_url( 'vendor/select2/dist/js/select2.min.js', PB_FILE ), array( 'jquery' ), '4.0.5' );

		// Featherlight
		wp_enqueue_script( 'featherlight', plugins_url( 'vendor/featherlight/release/featherlight.min.js', PB_FILE ), array( 'jquery' ), '1.7.13' );
		
		// Match Height
		wp_enqueue_script( 'match-height', plugins_url( 'vendor/matchHeight/dist/jquery.matchHeight-min.js', PB_FILE ), array( 'jquery' ), '0.7.2' );

		// Core
		wp_enqueue_style( 'pb-editor', plugins_url( 'css/editor.min.css', PB_FILE ) );
		wp_enqueue_script( 'pb-editor', plugins_url( 'js/dist/editor.min.js', PB_FILE ), array( 'jquery' ), true );
	
		do_action( 'pb_editor_enqueue_scripts' );
	}

	public function render_scripts()
	{
		if ( ! $this->is_screen() ) 
		{
			return;
		}

		do_action( 'pb_editor_render_scripts' );
	}

	public function body_class( $class )
	{
		if ( ! $this->is_screen() ) 
		{
			return $class;
		}

		$class .= ' page-builder';

		return trim( $class );
	}
}

pb()->editor = new PB_Editor();

add_action( 'add_meta_boxes'	   , array( pb()->editor, 'add_meta_boxes' ) );
add_action( 'save_post'			   , array( pb()->editor, 'save_post' ), 10, 2 );
add_action( 'admin_enqueue_scripts', array( pb()->editor, 'enqueue_scripts' ) );
add_action( 'admin_print_scripts'  , array( pb()->editor, 'render_scripts' ) );
add_action( 'admin_body_class'     , array( pb()->editor, 'body_class' ) );

add_action( 'wp_ajax_pb_widget_picker'  , array( pb()->editor, 'widget_picker' ) );
add_action( 'wp_ajax_pb_widget_settings', array( pb()->editor, 'widget_settings' ) );
add_action( 'wp_ajax_pb_widget_preview' , array( pb()->editor, 'widget_preview' ) );

add_action( 'wp_ajax_pb_save_widget_preview', function()
{
	$model = $_POST['model'];

	$models = get_option( 'pb_widget_preview', array() );

	$models[ $model['id'] ] = $model;

	update_option( 'pb_widget_preview', $models );
});


