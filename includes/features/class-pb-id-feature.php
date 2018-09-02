<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_ID_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'id' );
	}

	public function widget_init( $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$widget->add_field( array
			(
				'key'           => 'id',
				'name'          => 'id',
				'title'         => __( 'ID' ),
				'description'   => __( '' ),
				'type'          => 'text',
				'default_value' => '',
				'order'         => PB_ORDER_TAB_ATTRIBUTES + 10
			));
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$value = isset( $instance['id'] ) ? sanitize_title( $instance['id'] ) : '';

			if ( $value ) 
			{
				$atts['id'] = $value;
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_ID_Feature' );