<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_BG_Overlay_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'bg_overlay' );
	
		add_filter( 'pb_widget_args', array( $this, 'widget_args' ), 10, 3 );
	}

	public function widget_init( $widget )
	{
		if ( ! $widget->supports( $this->id ) ) 
		{
			return;
		}

		$widget->add_field( array
		(
			'key'           => 'bg_overlay_color',
			'name'          => 'bg_overlay_color',
			'title'         => __( 'Background Overlay' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
							   (
									'' => PB_THEME_DEFAULTS
							   ), pb()->options->get( 'theme_colors' ) ),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_BACKGROUND + 90
		));

		$widget->add_field( array
		(
			'key'           => 'bg_overlay_opacity',
			'name'          => 'bg_overlay_opacity',
			'title'         => __( 'Background Overlay Opacity' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
							   (
									'' => PB_THEME_DEFAULTS
							   ) + pb()->options->get( 'opacities' ),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_BACKGROUND + 95
		));
	}

	public function widget_args( $args, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$color   = isset( $instance['bg_overlay_color'] ) ? $instance['bg_overlay_color'] : '';
			$opacity = isset( $instance['bg_overlay_opacity'] ) ? $instance['bg_overlay_opacity'] : '';

			if ( $color || $opacity ) 
			{
				$atts = array
				(
					'class' => 'pb-bg-overlay'
				);

				if ( $color ) 
				{
					$atts['class'] .= " bg-$color";
				}

				if ( $opacity ) 
				{
					$atts['class'] .= " pb-opacity-$opacity";
				}

				$args['after_widget'] = sprintf( '<div%s"></div>%s', pb_render_attributes( $atts ), $args['after_widget'] );
			}
		}

		return $args;
	}
	
	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$color   = isset( $instance['bg_overlay_color'] ) ? $instance['bg_overlay_color'] : '';
			$opacity = isset( $instance['bg_overlay_opacity'] ) ? $instance['bg_overlay_opacity'] : '';

			if ( $color || $opacity ) 
			{
				$atts['class'] .= " pb-bg-overlay-container";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_BG_Overlay_Feature' );
