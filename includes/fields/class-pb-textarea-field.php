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
			'rows' => 3,
			'html' => false,
		);

		$field = wp_parse_args( $field, $defaults );

		return $field;
	}

	public function render( $field )
	{
		$atts = array
		(
			'id'   => $field['id'],
			'name' => $field['name'],
			'rows' => $field['rows'],
		);

		$atts = array_filter( $atts );

		echo '<textarea' . pb_esc_attr( $atts ) . '>' . esc_textarea( $field['value'] ) . '</textarea>';
	}

	public function sanitize( $value, $field )
	{
		if ( ! $field['html'] ) 
		{
			$value = sanitize_textarea_field( $value );
		}

		return $value;
	}
}

pb()->field_types->register_field( 'PB_Textarea_Field' );
