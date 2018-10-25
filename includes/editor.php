<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Editor
{
	public function __construct()
	{
		add_action( 'add_meta_boxes'       , array( $this, 'add_meta_box' ), 5, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_ajax_pb_load'         , array( $this, 'load' ) );
		add_action( 'wp_ajax_pb_widget_picker', array( $this, 'widget_picker' ) );

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
			'post'      => $post->ID,
			'nonceName' => PB_NONCE_NAME,
			'nonce'     => wp_create_nonce( 'editor' ),
		);

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
			
			jQuery( document ).ready( function()
			{
				jQuery( '#pb-editor' ).pageBuilder( <?php echo json_encode( $options ); ?> );
			});

		</script>

		<?php
	}

	public function render_widget( $widget )
	{
		$controls = array
		(
			'add'    => array( 'title' => __( 'Add' )   , 'description' => __( 'Add widget.' )           , 'icon' => 'dashicons dashicons-plus' ),
			'edit'   => array( 'title' => __( 'Edit' )  , 'description' => __( 'Edit widget.' )          , 'icon' => 'dashicons dashicons-edit' ),
			'copy'   => array( 'title' => __( 'Copy' )  , 'description' => __( 'Copy widget.' )          , 'icon' => 'dashicons dashicons-admin-page' ),
			'delete' => array( 'title' => __( 'Delete' ), 'description' => __( 'Delete widget.' )        , 'icon' => 'dashicons dashicons-trash' ),
			'toggle' => array( 'title' => __( 'Toggle' ), 'description' => __( 'Toggle widget content.' ), 'icon' => 'pb-toggle-indicator' ),
		);

		?>

		<div class="pb-widget pb-<?php echo esc_attr( $widget->id ); ?>-widget" data-type="<?php echo esc_attr( $widget->id ); ?>">
			<div class="pb-widget-top">
				<h3 class="pb-widget-title"><?php echo esc_html( $widget->title ); ?></h3>
				<div class="pb-widget-controls">
					<?php foreach ( $controls as $control_id => $control ) : ?>
					<button type="button" class="pb-widget-control pb-widget-<?php echo esc_attr( $control_id ); ?>-control" title="<?php echo esc_attr( $control['title'] ); ?>">
						<span class="<?php echo esc_attr( $control['icon'] ); ?>" area-hidden="true"></span>
						<span class="screen-reader-text"><?php echo esc_html( $control['description'] ); ?></span>
					</button>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="pb-widget-inside">
				<div class="pb-widget-preview"></div>
				<div class="pb-widget-container"></div>
			</div>
		</div>

		<?php
	}

	public function render_widget_button( $widget )
	{
		?>

		<button class="pb-widget-button pb-<?php echo esc_attr( $widget->id ); ?>-widget-button" data-type="<?php echo esc_attr( $widget->id ); ?>">
			<span class="pb-widget-button-title"><?php echo esc_html( $widget->title ); ?></span>
			<span class="pb-widget-button-description"><?php echo $widget->description; ?></span>
		</button>

		<?php
	}

	public function widget_picker()
	{
		if ( ! wp_doing_ajax() ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		$widgets = pb()->widgets->get_widgets();

		$col = 'pb-col-12 pb-col-sm-6';

		if ( count( $widgets ) > 2 ) 
		{
			$col .= ' pb-col-md-4';
		}

		?>

		<div id="pb-widget-picker">

			<h2><?php esc_html_e( 'Available Widgets' ); ?></h2>

			<div class="pb-widgets">
				<div class="pb-row">
					<?php foreach ( $widgets as $widget ) : ?>
					<div class="<?php echo esc_attr( $col ); ?>">
						<?php $this->render_widget_button( $widget ); ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>

		</div>

		<?php

		wp_die();
	}

	public function load()
	{
		if ( ! wp_doing_ajax() ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		$post_id = $_POST['post'];

		$models = get_post_meta( $post_id, 'pb_models', true );

		if ( ! is_array( $models ) ) 
		{
			$models = array();
		}

		wp_send_json( array
		(
			'models' => $models,
		));
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
		wp_enqueue_script( 'featherlight', plugins_url( "assets/js/featherlight$min.js", PB_FILE ), array( 'jquery' ), '1.7.13', true );

		// Core
		wp_enqueue_style( 'pb-editor', plugins_url( 'assets/css/editor.css', PB_FILE ) );
		wp_enqueue_script( 'pb-editor', plugins_url( "assets/js/editor$min.js", PB_FILE ), array( 'jquery' ), false, true );
	}

	public function is_screen()
	{
		if ( ! is_admin() ) 
		{
			return false;
		}

		global $pagenow;

		if ( $pagenow != 'post.php' && $pagenow != 'post-new.php' ) 
		{
			return false;
		}

		global $typenow;

		return post_type_supports( $typenow, PB_POST_TYPE_FEATURE );
	}
}

pb()->editor = new PB_Editor();
