<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Block_Align_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'block_align' );
	}

	public function widget( $widget )
	{
		if ( $widget->supports( $this->id ) ) 
		{
			$widget->add_field( array
			(
				'key'           => "{$widget->id}_block_align",
				'name'          => 'block_align',
				'label'         => __( 'Block Alignment' ),
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
			$value = isset( $instance['block_align'] ) ? sanitize_html_class( $instance['block_align'] ) : null;

			if ( $value ) 
			{
				$atts['class'] .= " block-{$instance['block_align']}";
			}
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_Block_Align_Feature' );
