<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_BG_Image_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'bg_image' );
	}

	public function widget( $widget )
	{
		if ( ! $widget->supports( $this->id ) ) 
		{
			return;
		}

		$widget->add_field( array
		(
			'key'           => "{$widget->id}_bg_image",
			'name'          => 'bg_image',
			'label'         => __( 'Background Image' ),
			'description'   => '',
			'type'          => 'group',
			'sub_fields'    => array
			(
				array
				(
					'name'          => 'id',
					'label'         => __( 'Image' ),
					'description'   => '',
					'type'          => 'image',
					'wrapper'       => array( 'width' => 50 ),
				),

				array
				(
					'name'          => 'size',
					'label'         => __( 'Image Size' ),
					'description'   => __( "WordPress image size: 'thumbnail', 'large', 'medium', 'small' or custom." ),
					'type'          => 'text',
					'default_value' => 'large',
					'wrapper'       => array( 'width' => 50 ),
				),

				array
				(
					'name'          => 'type',
					'label'         => __( 'Type' ),
					'description'   => '',
					'type'          => 'select',
					'choices'       => array
					(
						''          => PB_CHOICE_DONT_SET,
						'repeat'    => __( 'Repeat' ),
						'repeat-x'  => __( 'Repeat x' ),
						'repeat-y'  => __( 'Repeat y' ),
						'no-repeat' => __( 'No repeat' ),
						'cover'     => __( 'Cover' ),
						'contain'   => __( 'Contain' ),
						'parallax'  => __( 'Parallax' ),
					),
					'default_value' => '',
					'wrapper'       => array( 'width' => 50 ),
				),

				array
				(
					'name'          => 'position',
					'label'         => __( 'Position' ),
					'description'   => '',
					'type'          => 'select',
					'choices'       => array
					(
						''              => PB_CHOICE_DONT_SET,
						'left-top'      => __( 'left top' ),
						'left-center'   => __( 'left center' ),
						'left-bottom'   => __( 'left bottom' ),
						'right-top'     => __( 'right top' ),
						'right-center'  => __( 'right center' ),
						'right-bottom'  => __( 'right bottom' ),
						'center-top'    => __( 'center top' ),
						'center-center' => __( 'center center' ),
						'center-bottom' => __( 'center bottom' ),
					),
					'default_value' => '',
					'wrapper'       => array( 'width' => 50 ),
				)
			),
			'category' => 'background',
			'order'    => 0,
		));
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		if ( $widget->supports( $this->id ) && isset( $instance['bg_image'] ) ) 
		{
			$id       = $instance['bg_image']['id'];
			$size     = $instance['bg_image']['size'];
			$type     = $instance['bg_image']['type'];
			$position = $instance['bg_image']['position'];

			// Image

			if ( $id && get_post_type( $id ) == 'attachment' ) 
			{
				list( $image_url ) = wp_get_attachment_image_src( $id, $size );

				$atts['style'] = sprintf( 'background-image: url(%s);', esc_url( $image_url ) );
			}

			// Type

			if ( $type == 'repeat' || $type == 'repeat-x' || $type == 'repeat-y' || $type == 'no-repeat' ) 
			{
				$atts['class'] .= " bg-repeat-$type";
			}

			else if ( $type == 'cover' || $type == 'contain' )
			{
				$atts['class'] .= " bg-size-{$type}";
			}

			else if ( $type == 'parallax' )
			{
				$atts['class'] .= ' parallax';
			}

			// Position

			if ( $position ) 
			{
				$atts['class'] .= " bg-position-{$position}";
			}

			return $atts;
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_BG_Image_Feature' );
