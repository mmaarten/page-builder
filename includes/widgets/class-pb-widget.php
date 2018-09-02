<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Widget extends PB_Supportable
{
	public $id          = null;
	public $title       = null;
	public $description = null;
	public $controls    = null;
	public $option_page = null;

	public function __construct( $id, $title, $args = array() )
	{
		parent::__construct();

		$defaults = array
		(
			'description' => '',
			'controls'    => array( 'edit', 'copy', 'delete', 'toggle' ),
			'features'    => array()
		);

		$args = wp_parse_args( $args, $defaults );

		extract( $args, EXTR_SKIP );

		$this->id          = $id;
		$this->title       = $title;
		$this->description = $description;
		$this->controls    = (array) $controls;
		$this->option_page = "{$this->id}_widget_settings";

		$this->add_support( $features );

		//

		add_action( 'init', array( $this, 'add_tab_fields' ), 999 );

		do_action( 'pb/widget', $this );
		do_action( "pb/widget/type={$this->id}", $this );

		add_filter( "pb/widget_html_attributes/type={$this->id}", array( $this, 'widget_html_attributes' ), 5, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_scripts' ), 5 );
	}

	public function add_tab_fields()
	{
		$tabs = array
		(
			'general'    => array( 'title' => __( 'General' ), 'order' => PB_ORDER_TAB_GENERAL ),
			'layout'     => array( 'title' => __( 'Layout' ), 'order' => PB_ORDER_TAB_LAYOUT ),
			'spacing'    => array( 'title' => __( 'spacing' ), 'order' => PB_ORDER_TAB_SPACING ),
			'background' => array( 'title' => __( 'Background' ), 'order' => PB_ORDER_TAB_BACKGROUND ),
			'attributes' => array( 'title' => __( 'Attributes' ), 'order' => PB_ORDER_TAB_ATTRIBUTES )
		);

		uasort( $tabs, 'pb_sort_order' );

		$fields = $this->get_fields( array( 'parent' => '' ) );

		foreach ( $tabs as $tab_key => $tab ) 
		{
			$include = false;

			$next = next( $tabs );

			foreach ( $fields as $field ) 
			{
				if ( $next ) 
				{
					if ( $field['order'] > $tab['order'] && $field['order'] < $next['order'] ) 
					{
						$include = true;

						break;
					}
				}

				else if ( $field['order'] > $tab['order'] ) 
				{
					$include = true;

					break;
				}
			}

			if ( $include ) 
			{
				$this->add_field( array
				(
					'key'           => $tab_key,
					'name'          => $tab_key,
					'title'         => $tab['title'],
					'description'   => '',
					'type'          => 'tab',
					'order'         => $tab['order']
				));
			}
		}
	}

	public function get_defaults()
	{
		$defaults = pb()->fields->get_defaults( $this->option_page );

		return is_array( $defaults ) ? $defaults : array();
	}

	public function get_fields( $search = array() )
	{
		$fields = pb()->fields->get_fields( $this->option_page, $search );

		return is_array( $fields ) ? $fields : array();
	}

	public function add_field( $field )
	{
		$defaults = array
		(
			'preview' => false,
			'page'    => $this->option_page
		);

		$field = wp_parse_args( $field, $defaults );

		pb()->fields->add_field( $field );
	}

	public function widget_html_attributes( $atts, $instance )
	{
		return $atts;
	}

	public function widget( $args, $instance )
	{

	}

	public function form()
	{
		?>

		<div id="pb-<?php echo esc_attr( $this->id ); ?>-widget-settings" class="widget-settings">

			<h1><?php printf( esc_html__( '%s Settings' ), $this->title ); ?></h1>

			<?php do_action( 'pb/widget_settings_after_title', $this ); ?>

			<form method="post">
				
				<?php pb()->fields->settings_fields( $this->option_page ); ?>
				<?php pb()->fields->render_fields( $this->option_page ); ?>

				<?php submit_button( __( 'Update' ) ); ?>

			</form>

		</div>

		<?php
	}

	public function render_controls()
	{
		$available_controls = array
		(
			'add'    => array( 'title' => __( 'Add' )   , 'icon' => 'plus' ),
			'edit'   => array( 'title' => __( 'Edit' )  , 'icon' => 'edit' ),
			'copy'   => array( 'title' => __( 'Copy' )  , 'icon' => 'admin-page' ),
			'delete' => array( 'title' => __( 'Delete' ), 'icon' => 'trash' ),
			'toggle' => array( 'title' => __( 'Toggle' ), 'icon' => '' )
		);

		$controls = array_intersect_key( $available_controls, array_flip( $this->controls ) );

		if ( ! count( $controls ) ) 
		{
			return;
		}

		?>

		<div class="pb-widget-controls">
			<?php foreach ( $controls as $id => $control ) : ?>
			<button type="button" class="pb-widget-control pb-widget-<?php echo esc_attr( $id ); ?>-control" data-type="<?php echo esc_attr( $id ); ?>">
				<span class="dashicons dashicons-<?php echo esc_attr( $control['icon'] ); ?>"></span>
				<span class="screen-reader-text"><?php echo esc_html( $control['title'] ); ?></span>
			</button>
			<?php endforeach; ?>
		</div>

		<?php
	}

	public function editor_widget()
	{
		?>

		<div class="pb-widget pb-<?php echo esc_attr( $this->id ); ?>-widget open" data-type="<?php echo esc_attr( $this->id ); ?>">

			<div class="pb-widget-top">
				<h3 class="pb-widget-title"><?php echo esc_html( $this->title ); ?></h3>
				<?php $this->render_controls(); ?>
			</div>

			<div class="pb-widget-inside">
				<div class="pb-widget-preview"></div>
				<div class="pb-widget-container"></div>
			</div>

			<div class="pb-widget-description">

				<button type="button" class="pb-widget-button" data-type="<?php echo esc_attr( $this->id ); ?>">
					<span class="pb-widget-button-title"><?php echo esc_html( $this->title ); ?></span>
					<?php echo $this->description; ?>
				</button>
				
			</div>

		</div>

		<?php
	}

	public function preview_meta( $instance )
	{
		$fields = pb()->fields->get_fields( $this->option_page );

		if ( ! $fields ) 
		{
			return;
		}

		$preview_fields = wp_filter_object_list( $fields, array( 'preview' => true ) );

		if ( ! $preview_fields ) 
		{
			return;
		}

		?>

		<ul class="pb-widget-preview-meta">
			<?php foreach ( $preview_fields as $field ) : 
				
				$value = isset( $instance[ $field['name'] ] ) ? $instance[ $field['name'] ] : $field['default_value'];

				$field_type = pb()->field_types->get_field_type( $field['type'] );

				if ( $field_type ) 
				{
					$value = $field_type->translate( $value, $field );
				}

				$value = (string) $value;

				$text = $value ? $value : __( '(not set)' );
			?>
			<li><strong><?php echo esc_html( $field['title'] ); ?></strong><?php echo $text; ?></li>
			<?php endforeach; ?>
		</ul>

		<?php
	}

	public function preview( $instance )
	{
		$this->preview_meta( $instance );
	}

	public function maybe_enqueue_scripts()
	{
		if ( ! pb()->widgets->post_has_widget( 0, $this->id ) ) 
		{
			return;
		}

		$this->enqueue_scripts();
	}

	public function enqueue_scripts()
	{

	}
}
