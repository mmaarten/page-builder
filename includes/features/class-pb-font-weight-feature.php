<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Font_Weight_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'font_weight' );
	}

	public function widget_init( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => 'font_weight',
				'name'          => 'font_weight',
				'title'         => __( 'Font Weight' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => array
				(
					''       => PB_THEME_DEFAULTS,
					'light'  => __( 'Light' ),
					'normal' => __( 'Normal' ),
					'bold'   => __( 'Bold' )
				),
				'default_value' => '',
				'order'         => PB_ORDER_TAB_LAYOUT + 30
			));
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$value = isset( $instance['font_weight'] ) ? sanitize_html_class( $instance['font_weight'] ) : '';

			if ( $value ) 
			{
				$atts['class'] .= " font-weight-$value";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_Font_Weight_Feature' );
