<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Parallax_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'parallax' );
	}

	public function widget_init( $widget )
	{
		if ( ! $widget->supports( $this->id ) ) 
		{
			return;
		}

		$widget->add_field( array
		(
			'key'           => 'parallax',
			'name'          => 'parallax',
			'title'         => __( 'Parallax' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_BACKGROUND + 80
		));
	}
	
	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( $widget->supports( $this->id ) )
		{
			if ( isset( $instance['parallax'] ) && $instance['parallax'] ) 
			{
				$atts['class'] .= ' pb-parallax';
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_Parallax_Feature' );
