<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Text_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'text' );
	}

	public function render( $field )
	{
		// Attributes

		$atts = array
		(
			'type'  => 'text',
			'id'    => $field['id'],
			'name'  => $field['name'],
			'value' => $field['value']
		);

		$atts = array_filter( $atts );

		// Output

		printf( '<input%s>', pb_render_attributes( $atts ) );
	}

	public function sanitize( $value, $field )
	{
		return sanitize_text_field( $value );
	}

	public function translate( $value, $field )
	{
		return esc_html( $value );
	}
}

pb()->field_types->register( 'PB_Text_Field' );