<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Widget
{
	public $id          = null;
	public $title       = null;
	public $description = null;
	public $field_group = null;

	public function __construct( $id, $title, $args = array() )
	{
		$defaults = array
		(
			'description' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->id          = $id;
		$this->title       = $title;
		$this->description = $args['description'];
		$this->field_group = "{$this->id}_widget";

		do_action( 'pb/widget', $this );
	}

	public function get_fields()
	{
		return pb()->fields->get_fields( array( 'group' => $this->field_group ) );
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
			'group'   => $this->field_group,
			'preview' => false,
		);

		// Create
		$field = wp_parse_args( $args, $defaults );

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
