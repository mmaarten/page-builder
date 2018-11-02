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
			
			pb.init( '#pb-editor', <?php echo json_encode( $options ); ?> );

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
		$controls = array
		(
			'add'    => array( 'title' => __( 'Add' )   , 'description' => __( 'Add widget' )           , 'icon' => 'dashicons dashicons-plus' ),
			'edit'   => array( 'title' => __( 'Edit' )  , 'description' => __( 'Edit widget' )          , 'icon' => 'dashicons dashicons-edit' ),
			'copy'   => array( 'title' => __( 'Copy' )  , 'description' => __( 'Copy widget' )          , 'icon' => 'dashicons dashicons-admin-page' ),
			'delete' => array( 'title' => __( 'Delete' ), 'description' => __( 'Delete widget' )        , 'icon' => 'dashicons dashicons-trash' ),
			'toggle' => array( 'title' => __( 'Toggle' ), 'description' => __( 'Toggle widget content' ), 'icon' => 'pb-toggle-indicator' ),
		);

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

		// Output

		$widgets = pb()->widgets->get_widgets();

		?>

		<div id="pb-widget-picker">

			<h1><?php esc_html_e( 'Available Widgets' ); ?></h1>

			<div class="pb-available-widgets">
				<?php foreach ( $widgets as $widget ) : ?>
				<?php $this->render_widget( $widget ); ?>
				<?php endforeach; ?>
			</div>

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

		// Gets models

		$models = array();

		if ( metadata_exists( 'post', $post_id, 'pb_models' ) ) 
		{
			$models = get_post_meta( $post_id, 'pb_models', true );
		}

		// Response

		wp_send_json( array
		(
			'models' => $models,
		));
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
		$models  = isset( $_POST['models'] ) ? $_POST['models'] : array();

		// Save models

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
