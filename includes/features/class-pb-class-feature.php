<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Class_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'class' );
	}

	public function widget_init( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => 'class',
				'name'          => 'class',
				'title'         => __( 'Class' ),
				'description'   => __( 'Extra CSS classes.' ),
				'type'          => 'text',
				'default_value' => '',
				'order'         => PB_ORDER_TAB_ATTRIBUTES + 20 // Below ID field
			));
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			if ( isset( $instance['class'] ) && $value = pb_sanitize_html_class( $instance['class'] ) ) 
			{
				$atts['class'] .= " $value";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_Class_Feature' );