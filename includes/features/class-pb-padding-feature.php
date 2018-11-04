<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Padding_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'padding' );
	}

	public function get_features( $widget = null )
	{
		$features = array
		( 
			'pt' => __( 'Top' ),
			'pr' => __( 'Right' ),
			'pb' => __( 'Bottom' ),
			'pl' => __( 'Left' ),
		);

		if ( $widget ) 
		{
			return array_intersect_key( $features, $widget->get_features() );
		}

		return $features;
	}

	public function get_choices( $breakpoint = null )
	{
		$choices = apply_filters( 'pb/choices', array
		(
			'0'    => __( 'None' ),
			'1'    => __( 'Extra small' ),
			'2'    => __( 'Small' ),
			'3'    => __( 'Medium' ),
			'4'    => __( 'Large' ),
			'5'    => __( 'Extra large' ),
			'auto' => __( 'Auto' ),
		), 'padding' );

		if ( ! $breakpoint ) 
		{
			return $choices;
		}

		if ( $breakpoint == 'xs' ) 
		{
			return array( '' => PB_CHOICE_DONT_SET ) + $choices;
		}

		return array( '' => PB_CHOICE_INHERIT ) + $choices;
	}

	public function widget( $widget )
	{
		// Check support

		$features = $this->get_features( $widget );

		if ( ! $features ) 
		{
			return;
		}

		// Add fields

		$sub_fields = array();

		foreach ( pb_get_grid_breakpoints() as $breakpoint => $format ) 
		{
			$choices = $this->get_choices( $breakpoint );

			// Tab
			$sub_fields[] = array
			(
				'name'  => "tab_{$breakpoint}",
				'label' => strtoupper( $breakpoint ),
				'type'  => 'tab',
			);

			// Directions
			foreach ( $features as $feature => $label ) 
			{
				$sub_fields[] = array
				(
					'name'          => "{$feature}_{$breakpoint}",
					'label'         => $label,
					'type'          => 'select',
					'choices'       => $choices,
					'default_value' => '',
					'wrapper'       => array( 'width' => 100 / count( $features ) ),
				);
			}
		}
		
		$widget->add_field( array
		(
			'key'           => "{$widget->id}_padding",
			'name'          => 'padding',
			'label'         => __( 'Padding' ),
			'description'   => __( 'Set padding per screen size.' ),
			'type'          => 'group',
			'sub_fields'    => $sub_fields,
			'category'      => 'spacing',
			'order'         => 10,
		));
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		$features = $this->get_features( $widget );

		if ( $features ) 
		{
			foreach ( pb_get_grid_breakpoints() as $breakpoint => $format ) 
			{
				foreach ( $features as $feature => $label ) 
				{
					$name  = "{$feature}_{$breakpoint}";
					$value = isset( $instance['padding'][ $name ] ) ? $instance['padding'][ $name ] : null;

					if ( $value ) 
					{
						$atts['class'] .= ' ' . sprintf( $format, $feature, $value );
					}
				}
			}
		}

		return $atts;
	}
}

pb()->features->register_feature( 'PB_Padding_Feature' );
