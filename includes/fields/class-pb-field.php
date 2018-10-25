<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Field
{
	public $id = null;

	public function __construct( $id )
	{
		$this->id = $id;
	}
}
