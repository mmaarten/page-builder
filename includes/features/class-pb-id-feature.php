<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_ID_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'id' );
	}

	public function widget( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => "{$widget->id}_id",
				'name'          => 'id',
				'label'         => __( 'ID' ),
				'description'   => '',
				'type'          => 'text',
				'default_value' => '',
				'category'      => 'attributes',
			));
		}
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$value = isset( $instance['id'] ) ? sanitize_title( $instance['id'] ) : null;

			if ( $value ) 
			{
				$atts['id'] = $value;
			}
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_ID_Feature' );
