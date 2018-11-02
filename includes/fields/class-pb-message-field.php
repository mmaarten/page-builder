<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Message_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'message' );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'message' => '',
		);

		$field = wp_parse_args( $field, $defaults );

		return $field;
	}

	public function render( $field )
	{
		echo $field['message'];
	}
}

pb()->field_types->register_field( 'PB_Message_Field' );
