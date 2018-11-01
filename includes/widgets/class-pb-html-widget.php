<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_HTML_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'html', __( 'Custom HTML' ), array
		(
			'description' => __( 'Arbitrary HTML code.' ),
		));
	}
}

pb()->widgets->register_widget( 'PB_HTML_Widget' );
