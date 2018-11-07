<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Select_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'select' );
	}

	public function field( $field )
	{
		if ( ! isset( $field['choices'] ) || ! is_array( $field['choices'] ) ) 
		{
			$field['choices'] = array();
		}

		return $field;
	}

	public function render( $field )
	{
		$atts = array
		(
			'id'    => $field['id'],
			'name'  => $field['name'],
		);

		$atts = array_filter( $atts );

		echo '<select' . pb_esc_attr( $atts ) . '>';

		pb_dropdown_options( $field['choices'], $field['value'] );

		echo '</select>';
	}

	public function sanitize( $value, $field )
	{
		return $value;
	}

	public function translate( $value, $field )
	{
		if ( isset( $field['choices'][ $value ] ) ) 
		{
			$value = $field['choices'][ $value ];
		}

		return esc_html( $value );
	}
}

pb()->field_types->register_field( 'PB_Select_Field' );
