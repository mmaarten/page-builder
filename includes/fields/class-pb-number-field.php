<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Number_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'number' );
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
		
	}

	public function sanitize( $value, $field )
	{
		return $value;
	}
}

pb()->field_types->register_field( 'PB_Number_Field' );
