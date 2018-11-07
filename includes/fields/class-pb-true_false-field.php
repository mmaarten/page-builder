<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_True_False_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'true_false' );
	}

	public function field( $field )
	{
		return $field;
	}

	public function prepare( $field )
	{
		return $field;
	}

	public function render( $field )
	{
		$choices = array
		(
			'0' => __( 'No' ),
			'1' => __( 'Yes' ),
		);

		$atts = array
		(
			'id'    => $field['id'],
			'name'  => $field['name'],
		);

		$atts = array_filter( $atts );

		echo '<select' . pb_esc_attr( $atts ) . '>';

		pb_dropdown_options( $choices, $field['value'] );

		echo '</select>';
	}

	public function sanitize( $value, $field )
	{
		return $value ? 1 : 0;
	}

	public function translate( $value, $field )
	{
		return $value ? __( 'Yes' ) : __( 'No' );
	}
}

pb()->field_types->register_field( 'PB_True_False_Field' );
