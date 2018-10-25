<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Row_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'row', __( 'Row' ), array
		(
			'description' => __( 'Displays a row.' ),
		));
	}
}

pb()->widgets->register_widget( 'PB_Row_Widget' );
