<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Textarea_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'textarea' );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'rows'  => 5,
			'html'  => false
		);

		return wp_parse_args( $field, $defaults );
	}

	public function render( $field )
	{
		// Attributes

		$atts = array
		(
			'id'    => $field['id'],
			'name'  => $field['name'],
			'rows'  => $field['rows'],
		);

		$atts = array_filter( $atts );

		// Output

		printf( '<textarea%s>%s</textarea>', pb_render_attributes( $atts ), esc_textarea( $field['value'] ) );
	}

	public function sanitize( $value, $field )
	{
		if ( ! $field['html'] ) 
		{
			$value = sanitize_textarea_field( $value );
		}

		return $value;
	}

	public function translate( $value, $field )
	{
		if ( ! $field['html'] ) 
		{
			$value = esc_html( $value );
		}

		return $value;
	}
}

pb()->field_types->register( 'PB_Textarea_Field' );