<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Heading_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'heading', __( 'Heading' ), array
		(
			'description' => __( 'Displays a heading.' ),
		));
	}
}

pb()->widgets->register_widget( 'PB_Heading_Widget' );
