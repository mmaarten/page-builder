<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_True_False_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'true_false' );
	}

	public function render( $field )
	{
		// Attributes

		$atts = array
		(
			'id'       => $field['id'],
			'name'     => $field['name'],
			'selected' => $field['value'] ? 'selected' : ''
		);

		$atts = array_filter( $atts );

		// Choices

		$choices = array
		(
			'0' => __( 'No' ),
			'1' => __( 'Yes' )
		);

		// Output

		printf( '<select%s>', pb_render_attributes( $atts ) );
		
		pb_dropdown_options( $choices, $field['value'] );

		echo '</select>';
	}

	public function sanitize( $value, $field )
	{
		return $value ? 1 : 0;
	}

	public function translate( $value, $field )
	{
		return $value ? esc_html__( 'Yes' ) : esc_html__( 'No' );
	}
}

pb()->field_types->register( 'PB_True_False_Field' );