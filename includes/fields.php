<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Fields
{
	protected $fields = array();

	public function __construct()
	{

	}
}

pb()->fields = new PB_Fields();
