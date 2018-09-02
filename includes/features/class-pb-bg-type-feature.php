<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_BG_Type_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'bg_type' );
	}

	public function widget_init( $widget )
	{
		if ( ! $widget->supports( $this->id ) ) 
		{
			return;
		}

		$widget->add_field( array
		(
			'key'           => 'bg_type',
			'name'          => 'bg_type',
			'title'         => __( 'Background Type' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				''         => PB_THEME_DEFAULTS,
				'contain'  => __( 'Contain' ),
				'cover'    => __( 'Cover' ),
				'repeat-x' => __( 'Repeat horizontally' ),
				'repeat-y' => __( 'Repeat vertically' ),
				'repeat'   => __( 'Repeat horizontally and vertically' )
			),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_BACKGROUND + 70
		));
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$value = isset( $instance['bg_type'] ) ? sanitize_html_class( $instance['bg_type'] ) : '';

			if ( $value ) 
			{
				$atts['class'] .= " pb-bg-$value";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_BG_Type_Feature' );

