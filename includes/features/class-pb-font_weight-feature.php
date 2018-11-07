<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Font_Weight_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'font_weight' );
	}

	public function widget( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => "{$widget->id}_font_weight",
				'name'          => 'font_weight',
				'label'         => __( 'Font Weight' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => array
				(
					''       => PB_CHOICE_DONT_SET,
					'light'  => __( 'Light' ),
					'normal' => __( 'Normal' ),
					'bold'   => __( 'Bold' ),
				),
				'default_value' => '',
				'category'      => 'layout',
			));
		}
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$value = isset( $instance['font_weight'] ) ? sanitize_html_class( $instance['font_weight'] ) : null;

			if ( $value ) 
			{
				$atts['class'] .= " font-weight-{$instance['font_weight']}";
			}
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_Font_Weight_Feature' );
