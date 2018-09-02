<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_BG_Position_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'bg_position' );
	}

	public function widget_init( $widget )
	{
		if ( ! $widget->supports( $this->id ) ) 
		{
			return;
		}

		$widget->add_field( array
		(
			'key'           => 'bg_position',
			'name'          => 'bg_position',
			'title'         => __( 'Background Position' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				''              => PB_THEME_DEFAULTS,
				'left-top'      => __( 'left top' ),
				'left-center'   => __( 'left center' ),
				'left-bottom'   => __( 'left bottom' ),
				'center-top'    => __( 'center top' ),
				'center-center' => __( 'center center' ),
				'center-bottom' => __( 'center bottom' ),
				'right-top'     => __( 'right top' ),
				'right-center'  => __( 'right center' ),
				'right-bottom'  => __( 'right bottom' )
			),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_BACKGROUND + 40
		));
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			$value = isset( $instance['bg_position'] ) ? sanitize_html_class( $instance['bg_position'] ) : '';

			if ( $value ) 
			{
				$atts['class'] .= " pb-bg-$value";
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_BG_Position_Feature' );

