<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_BG_Color_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'bg_color' );
	}

	public function widget( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => "{$widget->id}_bg_color",
				'name'          => 'bg_color',
				'label'         => __( 'Background Color' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => array
				(
					''            => PB_CHOICE_DONT_SET,
					'primary'     => __( 'Primary' ),
					'secondary'   => __( 'Secondary' ),
					'success'     => __( 'Success' ),
					'danger'      => __( 'Danger' ),
					'warning'     => __( 'Warning' ),
					'info'        => __( 'Info' ),
					'light'       => __( 'Light' ),
					'dark'        => __( 'Dark' ),
					'white'       => __( 'White' ),
					'transparent' => __( 'Transparent' ),

				),
				'default_value' => '',
				'category'      => 'background',
			));
		}
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$value = isset( $instance['bg_color'] ) ? sanitize_html_class( $instance['bg_color'] ) : null;

			if ( $value ) 
			{
				$atts['class'] .= " bg-{$instance['bg_color']}";
			}
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_BG_Color_Feature' );
