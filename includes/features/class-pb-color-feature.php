<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Color_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'color' );
	}

	public function widget( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => "{$widget->id}_color",
				'name'          => 'color',
				'label'         => __( 'Color' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => array
				(
					''          => PB_CHOICE_DONT_SET,
					'primary'   => __( 'Primary' ),
					'secondary' => __( 'Secondary' ),
					'success'   => __( 'Success' ),
					'danger'    => __( 'Danger' ),
					'warning'   => __( 'Warning' ),
					'info'      => __( 'Info' ),
					'light'     => __( 'Light' ),
					'dark'      => __( 'Dark' ),
					'body'      => __( 'Body' ),
					'muted'     => __( 'Muted' ),
					'white'     => __( 'White' ),
				),
				'default_value' => '',
				'category'      => 'layout',
				'order'         => 10,
			));
		}
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$value = isset( $instance['color'] ) ? sanitize_html_class( $instance['color'] ) : null;

			if ( $value ) 
			{
				$atts['class'] .= " text-{$instance['color']}";
			}
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_Color_Feature' );
