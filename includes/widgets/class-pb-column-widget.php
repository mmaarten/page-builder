<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Column_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'column', __( 'Column' ), array
		(
			'description' => __( 'Displays a column.' ),
		));
	}
}

pb()->widgets->register_widget( 'PB_Column_Widget' );
