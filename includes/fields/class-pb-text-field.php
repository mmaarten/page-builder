<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Text_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'text' );
	}
}

pb()->field_types->register_field( 'PB_Text_Field' );
