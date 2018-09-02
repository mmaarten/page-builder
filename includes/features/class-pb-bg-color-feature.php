<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_BG_Color_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'bg_color' );
	}

	public function widget_init( $widget )
	{
		if ( ! $widget->supports( $this->id ) ) 
		{
			return;
		}

		$widget->add_field( array
		(
			'key'           => 'bg_color',
			'name'          => 'bg_color',
			'title'         => __( 'Background Color' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				'' => PB_THEME_DEFAULTS,
			), pb()->options->get( 'theme_colors' ) ),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_BACKGROUND + 10
		));
	}
	
	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$value = isset( $instance['bg_color'] ) ? sanitize_html_class( $instance['bg_color'] ) : '';

			if ( $value ) 
			{
				$atts['class'] .= " bg-$value";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_BG_Color_Feature' );
