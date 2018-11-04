<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Image_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'image', __( 'Image' ), array
		(
			'description' => __( 'Displays an image.' ),
			'features'    => array( 'id', 'class', 'block_align', 'mt', 'mr', 'mb', 'ml', 'pt', 'pr', 'pb', 'pl' ),
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_image",
			'name'          => 'image',
			'label'         => __( 'Image' ),
			'description'   => '',
			'type'          => 'image',
			'default_value' => '',
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_type",
			'name'          => 'type',
			'label'         => __( 'Type' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				''          => PB_CHOICE_DONT_SET,
				'thumbnail' => __( 'Thumbnail' ),
				'rounded'   => __( 'Rounded' ),
			),
			'default_value' => '',
			'category'      => 'layout',
		));
	}
}

pb()->widgets->register_widget( 'PB_Image_Widget' );
