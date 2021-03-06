<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Class_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'class' );
	}

	public function widget( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => "{$widget->id}_class",
				'name'          => 'class',
				'label'         => __( 'Class' ),
				'description'   => __( 'Additional CSS classes.' ),
				'type'          => 'text',
				'default_value' => '',
				'category'      => 'attributes',
				'order'         => 10,
			));
		}
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			if ( isset( $instance['class'] ) ) 
			{
				// Sanitize class names

				$classes = array();

				foreach ( explode( ' ', $instance['class' ] ) as $class ) 
				{
					$class = sanitize_html_class( $class );

					if ( $class ) 
					{
						$classes[ $class ] = $class;
					}
				}

				// set class names

				if ( $classes ) 
				{
					$atts['class'] .= ' ' . implode( ' ', $classes );
				}
			}
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_Class_Feature' );
