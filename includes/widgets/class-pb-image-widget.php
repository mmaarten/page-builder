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
			'key'           => "{$this->id}_image_size",
			'name'          => 'image_size',
			'label'         => __( 'Image Size' ),
			'description'   => __( "WordPress image size: 'thumbnail', 'large', 'medium', 'small' or custom." ),
			'type'          => 'text',
			'default_value' => 'large',
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_link",
			'name'          => 'link',
			'label'         => __( 'Link' ),
			'description'   => '',
			'type'          => 'url',
			'default_value' => '',
			'preview'       => true,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_link_tab",
			'name'          => 'link_tab',
			'label'         => __( 'Open link in new window.' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
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
				''              => PB_CHOICE_DONT_SET,
				'img-thumbnail' => __( 'Thumbnail' ),
				'rounded'       => __( 'Rounded' ),
			),
			'default_value' => '',
			'category'      => 'layout',
		));
	}

	public function render( $args, $instance )
	{
		// Instance

		$instance = wp_parse_args( $instance, $this->get_defaults() );

		// Attributes

		$atts = array();

		if ( $instance['type'] ) 
		{
			$atts['class'] = $instance['type'];
		}

		$link = array();

		if ( $instance['link'] )
		{
			$link['href'] = esc_url( $instance['link'] );

			if ( $instance['link_tab'] ) 
			{
				$link['target'] = '_blank';
			}
		}

		// Output

		echo $args['before'];

		if ( $link ) 
		{
			echo '<a' . pb_esc_attr( $link ) . '>';
		}

		echo wp_get_attachment_image( $instance['image'], $instance['image_size'], false, $atts );

		if ( $link ) 
		{
			echo '</a>';
		}

		echo $args['after'];
	}
}

pb()->widgets->register_widget( 'PB_Image_Widget' );
