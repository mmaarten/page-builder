<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Text_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'text' );
	}

	public function render( $field )
	{
		$atts = array
		(
			'type'  => 'text',
			'id'    => $field['id'],
			'name'  => $field['name'],
			'value' => $field['value'],
		);

		$atts = array_filter( $atts );

		echo '<input' . pb_esc_attr( $atts ) . '>';
	}

	public function sanitize( $value, $field )
	{
		return sanitize_text_field( $value );
	}
}

pb()->field_types->register_field( 'PB_Text_Field' );
