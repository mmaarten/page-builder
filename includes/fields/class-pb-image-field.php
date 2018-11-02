<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Image_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'image' );
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
		if ( $value && get_post_type( $value ) == 'attachment' ) 
		{
			return $value;
		}

		return 0;
	}
}

pb()->field_types->register_field( 'PB_Image_Field' );
