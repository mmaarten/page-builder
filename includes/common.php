<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

function pb()
{
	static $instance = null;

	if ( ! $instance ) 
	{
		$instance = new PB();
	}

	return $instance;
}
