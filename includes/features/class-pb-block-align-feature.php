<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Block_Align_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'block_align' );
	}

	public function widget_init( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => 'block_align',
				'name'          => 'block_align',
				'title'         => __( 'Alignment' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => array
				(
					''       => PB_THEME_DEFAULTS,
					'left'   => __( 'Left' ),
					'center' => __( 'Center' ),
					'right'  => __( 'Right' )
					
				),
				'default_value' => '',
				'order'         => PB_ORDER_TAB_LAYOUT + 10
			));
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$value = isset( $instance['block_align'] ) ? sanitize_html_class( $instance['block_align'] ) : '';

			if ( $value ) 
			{
				$atts['class'] .= " block-$value";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_Block_Align_Feature' );