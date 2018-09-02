<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Color_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'color' );
	}

	public function widget_init( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => 'color',
				'name'          => 'color',
				'title'         => __( 'Text Color' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => array_merge( array
				(
					'' => PB_THEME_DEFAULTS,
				), pb()->options->get( 'theme_colors' ) ),
				'default_value' => '',
				'order'         => PB_ORDER_TAB_LAYOUT + 10
			));
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$value = isset( $instance['color'] ) ? sanitize_html_class( $instance['color'] ) : '';

			if ( $value ) 
			{
				$atts['class'] .= " text-$value";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_Color_Feature' );