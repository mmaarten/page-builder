<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Number_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'number' );
	}

	public function render( $field )
	{
		$defaults = array
		(
			'id'    => '',
			'class' => '',
			'name'  => '',
			'value' => '',
			'extra' => ''
		);

		$field = wp_parse_args( $field, $defaults );

		$atts = array
		(
			'type'  => 'text',
			'id'    => $field['id'],
			'class' => $field['class'],
			'name'  => $field['name'],
			'value' => $field['value']
		);

		$atts = array_filter( $atts );

		printf( '<input%s>', pb_render_attributes( $atts, $field['extra'] ) );
	}

	public function sanitize( $value, $field )
	{
		return intval( $value );
	}

	public function translate( $value, $field )
	{
		return (string) intval( $value );
	}
}

pb()->field_types->register( 'PB_Number_Field' );