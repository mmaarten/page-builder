<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_BG_Image_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'bg_image' );
	}

	public function widget_init( $widget )
	{
		if ( $widget->supports( 'bg_image' ) )
		{
			$widget->add_field( array
			(
				'key'           => 'bg_image',
				'name'          => 'bg_image',
				'title'         => __( 'Background Image' ),
				'description'   => '',
				'type'          => 'image',
				'default_value' => '',
				'order'         => PB_ORDER_TAB_BACKGROUND + 20
			));
		}

		if ( $widget->supports( 'bg_image_size' ) )
		{
			$widget->add_field( array
			(
				'key'           => 'bg_image_size',
				'name'          => 'bg_image_size',
				'title'         => __( 'Background Image Size' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => pb_get_image_size_choices(),
				'default_value' => 'large',
				'order'         => PB_ORDER_TAB_BACKGROUND + 30
			));
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( 'bg_image' ) && isset( $instance['bg_image'] ) && $instance['bg_image'] )
		{
			$image_size = 'large';

			if ( $widget->supports( 'bg_image_size' ) && $instance['bg_image'] ) 
			{
				$image_size = $instance['bg_image'];
			}

			list( $image_url ) = wp_get_attachment_image_src( $instance['bg_image'], $image_size );

			if ( $image_url ) 
			{
				if ( ! isset( $atts['style'] ) ) 
				{
					$atts['style'] = '';
				}

				$atts['style'] .= "background-image:url($image_url);";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_BG_Image_Feature' );