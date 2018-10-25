<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Button_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'button', __( 'Button' ), array
		(
			'description' => __( 'Displays a button.' ),
		));
	}
}

pb()->widgets->register_widget( 'PB_Button_Widget' );
