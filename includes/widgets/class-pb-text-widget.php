<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Text_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'text', __( 'Text' ), array
		(
			'description' => __( 'Arbitrary text.' ),
		));
	}
}

pb()->widgets->register_widget( 'PB_Text_Widget' );
