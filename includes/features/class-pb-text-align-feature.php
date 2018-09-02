<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Text_Align_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'text_align' );
	}

	public function widget_init( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => 'text_align',
				'name'          => 'text_align',
				'title'         => __( 'Alignment' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => array_merge( array
				(
					'' => PB_THEME_DEFAULTS,
				), pb()->options->get( 'text_align' ) ),
				'default_value' => '',
				'order'         => PB_ORDER_TAB_LAYOUT + 10
			));
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$value = isset( $instance['text_align'] ) ? sanitize_html_class( $instance['text_align'] ) : '';

			if ( $value ) 
			{
				$atts['class'] .= " text-$value";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_Text_Align_Feature' );