<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Editor
{
	public function __construct()
	{
		add_action( 'add_meta_boxes'            , array( $this, 'add_meta_box' ), 5, 2 );
		add_action( 'admin_enqueue_scripts'     , array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_pb_load'           , array( $this, 'load' ) );
		add_action( 'wp_ajax_pb_save'           , array( $this, 'save' ) );
		add_action( 'wp_ajax_pb_widget_picker'  , array( $this, 'widget_picker' ) );
		add_action( 'wp_ajax_pb_widget_settings', array( $this, 'widget_settings' ) );
		add_action( 'wp_ajax_pb_widget_preview' , array( $this, 'widget_preview' ) );

		add_filter( 'pb/input_value', function( $value, $field )
		{
			$model = $_POST['model'];

			if ( isset( $model['data'][ $field['name'] ] ) ) 
			{
				$value = $model['data'][ $field['name'] ];
				$value = pb_stripslashes( $value );
			}

			return $value;

		}, 10, 2 );
	}

	public function get_available_widgets( $parent = null )
	{
		if ( $parent instanceof PB_Widget ) 
		{
			$parent = $parent->id;
		}

		$widgets = pb()->widgets->get_widgets();

		// Row
		if ( $parent == 'row' ) 
		{
			// Only columns
			$available = array( 'column' );
		}

		// Column
		elseif ( $parent == 'column' )
		{
			// All but columns
			$available = $widgets;

			unset( $available['column'] );

			$available = array_keys( $available );
		}

		// Other than row or column
		elseif ( $parent )
		{
			// None
			$available = array();
		}

		// No parent
		else
		{
			// All but columns
			$available = $widgets;

			unset( $available['column'] );

			$available = array_keys( $available );
		}

		// Filter
		$available = apply_filters( 'pb/available_widgets'                 , $available, $parent );
		$available = apply_filters( "pb/available_widgets/parent={$parent}", $available, $parent );

		// Get objects
		$available = array_intersect_key( $widgets, array_flip( $available ) );

		// Return
		return $available;
	}

	public function add_meta_box( $post_type, $post )
	{
		if ( ! $this->is_screen() ) 
		{
			return;
		}

		add_meta_box( 'pb-editor-meta-box', __( 'Page Builder' ), array( $this, 'render' ), null, 'advanced', 'high' );
	}

	public function render( $post )
	{
		$widgets = pb()->widgets->get_widgets();

		$options = array
		(
			'post'           => $post->ID,
			'nonceName'      => PB_NONCE_NAME,
			'nonce'          => wp_create_nonce( 'editor' ),
			'widgetDefaults' => array(),
		);

		foreach ( $widgets as $widget ) 
		{
			$options['widgetDefaults'][ $widget->id ] = $widget->get_defaults();
		}

		?>

		<div id="pb-editor" class="pb-editor">

			<?php wp_nonce_field( 'editor_render', PB_NONCE_NAME ); ?>

			<div class="pb-available-widgets">
				<?php foreach ( $widgets as $widget ) : ?>
				<?php $this->render_widget( $widget ); ?>
				<?php endforeach; ?>
			</div>

			<div class="pb-widgets"></div>

			<div class="pb-editor-footer">
				<button type="button" class="pb-add-widget-control"><?php esc_html_e( 'Add Widget' ); ?></button>
			</div>

		</div>

		<script type="text/javascript">
			
			jQuery( window ).on( 'load', function()
			{
				pb.init( '#pb-editor', <?php echo json_encode( $options ); ?> );
			});

		</script>

		<?php
	}

	public function render_widget( $widget )
	{
		?>

		<div class="pb-widget pb-<?php echo esc_attr( $widget->id ); ?>-widget" data-type="<?php echo esc_attr( $widget->id ); ?>">
			<div class="pb-widget-top">
				<h3 class="pb-widget-title"><?php echo esc_html( $widget->title ); ?></h3>
				<div class="pb-widget-controls">
					<?php $this->render_widget_controls( $widget ); ?>
				</div>
			</div>
			<div class="pb-widget-inside">
				<div class="pb-widget-preview"></div>
				<div class="pb-widget-container"></div>
			</div>
			<div class="pb-widget-description">
				<?php echo $widget->description; ?>
			</div>
		</div>

		<?php
	}

	public function render_widget_controls( $widget )
	{
		// Available controls

		$controls = array
		(
			'add'    => array( 'title' => __( 'Add' )   , 'description' => __( 'Add widget' )           , 'icon' => 'dashicons dashicons-plus' ),
			'edit'   => array( 'title' => __( 'Edit' )  , 'description' => __( 'Edit widget' )          , 'icon' => 'dashicons dashicons-edit' ),
			'copy'   => array( 'title' => __( 'Copy' )  , 'description' => __( 'Copy widget' )          , 'icon' => 'dashicons dashicons-admin-page' ),
			'delete' => array( 'title' => __( 'Delete' ), 'description' => __( 'Delete widget' )        , 'icon' => 'dashicons dashicons-trash' ),
			'toggle' => array( 'title' => __( 'Toggle' ), 'description' => __( 'Toggle widget content' ), 'icon' => 'pb-toggle-indicator' ),
		);

		// Check if widget can contain widgets

		$available_widgets = $this->get_available_widgets( $widget );

		// Remove 'add' control
		if ( ! $available_widgets ) 
		{
			unset( $controls['add'] );
		}

		// Output

		foreach ( $controls as $control_id => $control ) 
		{
			printf( '<button type="button" class="pb-widget-control pb-widget-%1$s-control" data-type="%1$s" title="%2$s">', 
				esc_attr( $control_id ), esc_attr( $control['title'] ) );

			if ( $control['icon'] ) 
			{
				printf( '<span class="%s" aria-hidden="true"></span>', esc_attr( $control['icon'] ) );
			}

			if ( $control['description'] ) 
			{
				printf( '<span class="screen-reader-text">%s</span>', esc_html( $control['description'] ) );
			}

			echo '</button>';
		}
	}

	public function widget_picker()
	{
		// Check ajax and referer

		if ( ! wp_doing_ajax() ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		// Post data

		$parent = isset( $_POST['parent'] ) ? $_POST['parent'] : null;

		//

		if ( $parent ) 
		{
			$parent = pb()->widgets->get_widget( $parent );
		}

		// Get available widgets
		$available = $this->get_available_widgets( $parent );

		// Output

		$cols = 'pb-col-12';

		if ( count( $available ) > 1 ) 
		{
			$cols .= ' pb-col-sm-6';

			if ( count( $available ) > 2 ) 
			{
				$cols .= ' pb-col-md-4 pb-col-lg-3';
			}
		}

		?>

		<div id="pb-widget-picker">

			<h1><?php esc_html_e( 'Available Widgets' ); ?></h1>

			<?php if ( $available ) : ?>
			<div class="pb-available-widgets">
				<div class="pb-row">
					<?php foreach ( $available as $widget ) : ?>
					<div class="<?php echo esc_attr( $cols ); ?>">
						<?php $this->render_widget( $widget ); ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php 

			elseif ( $parent ) :
			
				$name = sprintf( '<strong>%s</strong>', $parent->title );

				pb_admin_notice( sprintf( __( '%s widget cannot contain widgets.' ), $name ) );

			else :
			
				pb_admin_notice( __( 'No widgets available.' ) );
			
			endif;

			?>

		</div>

		<?php

		wp_die();
	}

	public function widget_settings()
	{
		// Check ajax and referer

		if ( ! wp_doing_ajax() ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		// Post data

		$model = $_POST['model'];

		// Output

		$widget = pb()->widgets->get_widget( $model['type'] );

		?>

		<div id="pb-widget-settings">

			<h1><?php printf( esc_html__( '%s Settings' ), $widget->title ); ?></h1>

			<form method="post">
				
				<?php pb()->fields->settings_fields( $widget->field_group ); ?>
				<?php pb()->fields->render_fields( $widget->field_group ); ?>

				<?php submit_button( __( 'Update' ) ); ?>

			</form>

		</div>

		<?php

		wp_die();
	}

	public function widget_preview()
	{
		// Check ajax and referer

		if ( ! wp_doing_ajax() ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		// Post data

		$models = isset( $_POST['models'] ) ? $_POST['models'] : array();

		//

		$preview = array();

		foreach ( $models as $key => $model ) 
		{
			// Get widget

			$widget = pb()->widgets->get_widget( $model['type'] );

			if ( ! $widget ) 
			{
				continue;
			}

			// Get instance

			$instance = isset( $model['data'] ) ? $model['data'] : array();
			$instance = pb_stripslashes( $instance );

			// Get preview content

			ob_start();

			$widget->preview( $instance );

			$preview[ $key ] = ob_get_clean();
		}

		// Response

		wp_send_json( $preview );
	}

	public function load()
	{
		// Check ajax and referer

		if ( ! wp_doing_ajax() ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		// Post data

		$post_id = $_POST['post'];

		// Models

		$models = array();

		if ( metadata_exists( 'post', $post_id, 'pb_models' ) ) 
		{
			$models = get_post_meta( $post_id, 'pb_models', true );
		}

		// Fields

		$fields = pb()->fields->get_fields();

		// Response

		$response = apply_filters( 'pb/editor_load_response', array
		(
			'models' => $models,
			'fields' => $fields,
		), $post_id );

		wp_send_json( $response );
	}

	public function save()
	{
		// Check ajax and referer

		if ( ! wp_doing_ajax() ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		// Post data

		$post_id = $_POST['post'];
		$models  = isset( $_POST['models'] ) ? pb_stripslashes( $_POST['models'] ) : array();
		$append  = $_POST['append'] ? true : false;

		// Save models

		if ( $append ) 
		{
			$post_models = get_post_meta( $post_id, 'pb_models', true );

			if ( is_array( $post_models ) )
			{
				$models = array_merge( $post_models, $models );
			}
		}

		update_post_meta( $post_id, 'pb_models', $models );

		// Response

		wp_send_json( array() );
	}

	public function enqueue_scripts()
	{
		if ( ! $this->is_screen() ) 
		{
			return;
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Vendor
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'featherlight', plugins_url( "assets/js/featherlight$min.js", PB_FILE ), array( 'jquery' ), '1.7.13' );

		do_action( 'pb/editor_enqueue_scripts' );

		// Core
		wp_enqueue_style( 'pb-editor', plugins_url( "assets/css/editor.min.css", PB_FILE ) );
		wp_enqueue_script( 'pb-editor', plugins_url( "assets/js/editor$min.js", PB_FILE ), array( 'jquery' ) );
	}

	public function is_screen()
	{
		// Check if admin area

		if ( ! is_admin() ) 
		{
			return false;
		}

		// Check if post edit screen

		global $pagenow;
		
		if ( $pagenow != 'post.php' && $pagenow != 'post-new.php' ) 
		{
			return false;
		}

		// Check post type support

		global $typenow;

		return post_type_supports( $typenow, PB_POST_TYPE_FEATURE );
	}
}

pb()->editor = new PB_Editor();
