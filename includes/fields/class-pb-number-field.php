<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Number_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'number' );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'min'   => '',
			'max'   => '',
			'step'  => '',
		);

		$field = wp_parse_args( $field, $defaults );

		return $field;
	}

	public function render( $field )
	{
		$atts = array
		(
			'type'  => 'number',
			'id'    => $field['id'],
			'name'  => $field['name'],
			'value' => $field['value'],
			'min'   => $field['min'],
			'max'   => $field['max'],
			'step'  => $field['step'],
		);

		$atts = array_filter( $atts );

		echo '<input' . pb_esc_attr( $atts ) . '>';
	}

	public function sanitize( $value, $field )
	{
		return intval( $value );
	}

	public function translate( $value, $field )
	{
		return intval( $value );
	}
}

pb()->field_types->register_field( 'PB_Number_Field' );
