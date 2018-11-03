<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Widget extends PB_Supportable
{
	public $id          = null;
	public $title       = null;
	public $description = null;
	public $field_group = null;

	public function __construct( $id, $title, $args = array() )
	{
		parent::__construct();

		$defaults = array
		(
			'description' => '',
			'features'    => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$this->id          = $id;
		$this->title       = $title;
		$this->description = $args['description'];
		$this->field_group = "{$this->id}_widget";

		$this->add_support( $args['features'] );

		do_action( 'pb/widget', $this );
	}

	public function get_fields( $search = null )
	{
		$search = wp_parse_args( $search, array
		(
			'group' => $this->field_group,
		));

		return pb()->fields->get_fields( $search );
	}

	public function get_defaults()
	{
		$defaults = array();

		foreach ( $this->get_fields() as $field ) 
		{
			$defaults[ $field['name'] ] = $field['default_value'];
		}

		return $defaults;
	}

	public function add_field( $args )
	{
		// Defaults
		$defaults = array
		(
			'group'    => $this->field_group,
			'preview'  => false,
			'category' => 'default',
			'order'    => 0,
		);

		// Create
		$field = wp_parse_args( $args, $defaults );

		// Category

		$categories = array
		(
			'default'    => array( 'title' => __( 'General' )   , 'order' => 0 ),
			'layout'     => array( 'title' => __( 'Layout' )    , 'order' => 1000 ),
			'background' => array( 'title' => __( 'Background' ), 'order' => 2000 ),
			'spacing'    => array( 'title' => __( 'Spacing' )   , 'order' => 3000 ),
			'attributes' => array( 'title' => __( 'Attributes' ), 'order' => 4000 ),
		);

		if ( isset( $categories[ $field['category'] ] ) ) 
		{
			$cat_id   = $field['category'];
			$cat_key  = "{$this->id}_category_{$cat_id}";
			$category = $categories[ $field['category'] ];

			$cat_field = $this->get_fields( array( 'key' => $cat_key ) );
			$cat_field = reset( $cat_field );

			if ( ! $cat_field ) 
			{
				$cat_field = array
				(
					'key'     => $cat_key,
					'label'   => $category['title'],
					'type'    => 'tab',
					'order'   => $category['order'],
					'group'   => $field['group'],
					'preview' => false,
				);

				pb()->fields->add_field( $cat_field );
			}

			$field['order'] += $cat_field['order'] + 1;
		}

		// Register field

		pb()->fields->add_field( $field );
	}

	public function render( $args, $instance )
	{
		
	}

	public function preview( $instance )
	{
		$this->preview_meta( $instance );
	}

	public function preview_meta( $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$meta = array();

		foreach ( $this->get_fields() as $field ) 
		{
			if ( ! $field['preview'] ) 
			{
				continue;
			}

			$value = $instance[ $field['name'] ];
			$value = apply_filters( "pb/translate_field/type={$field['type']}", $value, $field );

			if ( (string) $value === '' ) 
			{
				$value = __( '(not set)' );
			}

			$meta[] = array
			(
				'title'   => $field['label'],
				'content' => $value,
			);
		}

		if ( ! $meta ) 
		{
			return;
		}

		echo '<ul class="pb-widget-meta">';

		foreach ( $meta as $data ) 
		{
			printf( '<li><strong>%s</strong>: %s</li>', esc_html( $data['title'] ), $data['content'] );
		}

		echo '</ul>';
	}

	public function enqueue_scripts()
	{
		
	}
}
