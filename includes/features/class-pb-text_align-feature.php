<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Text_Align_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'text_align' );
	}

	public function widget( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => "{$widget->id}_text_align",
				'name'          => 'text_align',
				'label'         => __( 'Text Alignment' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => array
				(
					''       => PB_CHOICE_DONT_SET,
					'left'   => __( 'Left' ),
					'center' => __( 'Center' ),
					'right'  => __( 'Right' ),

				),
				'default_value' => '',
				'category'      => 'layout',
			));
		}
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$value = isset( $instance['text_align'] ) ? sanitize_html_class( $instance['text_align'] ) : null;

			if ( $value ) 
			{
				$atts['class'] .= " text-{$instance['text_align']}";
			}
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_Text_Align_Feature' );
