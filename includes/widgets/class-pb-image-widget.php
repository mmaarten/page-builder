<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Image_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'image', __( 'Image' ), array
		(
			'description' => __( 'Displays an image.' ),
		));
	}
}

pb()->widgets->register_widget( 'PB_Image_Widget' );
