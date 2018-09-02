<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Tab_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'tab' );
	}
}

pb()->field_types->register( 'PB_Tab_Field' );