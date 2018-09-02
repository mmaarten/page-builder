<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Message_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'message' );
	}

	public function render( $field )
	{
		$defaults = array
		(
			'message' => ''
		);

		$field = wp_parse_args( $field, $defaults );

		echo $field['message'];
	}
}

pb()->field_types->register( 'PB_Message_Field' );