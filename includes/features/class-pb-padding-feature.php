<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Padding_Feature extends PB_Feature
{
	protected $choices     = array();
	protected $breakpoints = array();

	public function __construct()
	{
		parent::__construct( 'padding' );

		$this->choices = array_merge( array
		(
			''  => PB_THEME_DEFAULTS,
		), pb()->options->get( 'spacers' ));
	}

	public function widget_init( $widget )
	{
		$all_directions = array
		(
			'padding_top'    => array( 'slug' => 'pt', 'title' => __( 'Top' ) ),
			'padding_right'  => array( 'slug' => 'pr', 'title' => __( 'Right' ) ),
			'padding_bottom' => array( 'slug' => 'pb', 'title' => __( 'Bottom' ) ),
			'padding_left'   => array( 'slug' => 'pl', 'title' => __( 'Left' ) )
		);

		$directions = array();

		foreach ( $all_directions as $feature => $direction ) 
		{
			if ( $widget->supports( $feature ) ) 
			{
				$directions[ $feature ] = $direction;
			}
		}

		if ( ! count( $directions ) ) 
		{
			return;
		}

		$widget->add_field( array
		(
			'key'                   => 'padding',
			'name'                  => 'padding',
			'title'                 => __( 'Padding' ),
			'description'           => __( 'Set padding per screen size.' ),
			'description_placement' => 'label',
			'type'                  => 'group',
			'layout'                => 'row',
			'order'                 => PB_ORDER_TAB_SPACING + 20
		));

		$order = 0;

		$breakpoints = pb()->options->get( 'grid_breakpoint_formats' );

		foreach ( $breakpoints as $breakpoint => $format ) 
		{
			$choices = $this->choices;

			if ( $breakpoint != 'xs' ) 
			{
				$choices = array
				(
					'' => PB_INHERIT_FROM_SMALLER,
				) + $choices;
			}

			$widget->add_field( array
			(
				'key'           => "padding_tab_{$breakpoint}",
				'name'          => 'tab',
				'title'         => strtoupper( $breakpoint ),
				'description'   => '',
				'type'          => 'tab',
				'default_value' => '',
				'order'         => $order,
				'parent'        => 'padding'
			));

			foreach ( $directions as $direction ) 
			{
				$widget->add_field( array
				(
					'key'           => "{$direction['slug']}_{$breakpoint}",
					'name'          => "{$direction['slug']}_{$breakpoint}",
					'title'         => $direction['title'],
					'type'          => 'select',
					'choices'       => $choices,
					'default_value' => '',
					'order'         => $order += 10,
					'parent'        => 'padding',
					'wrapper'       => array( 'width' => 100 / count( $directions ) )
				));
			}
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( ! $widget->supports( 'padding_top' )
			&& ! $widget->supports( 'padding_right' )
			&& ! $widget->supports( 'padding_bottom' )
			&& ! $widget->supports( 'padding_left' ) )
		{
			return $atts;
		}

		$data = isset( $instance['padding'] ) ? (array) $instance['padding'] : array();

		foreach ( pb()->options->get( 'grid_breakpoint_formats' ) as $breakpoint => $format ) 
		{
			if ( $widget->supports( 'padding_top' ) )
			{
				$value = isset( $data["pt_$breakpoint"] ) ? sanitize_html_class( $data["pt_$breakpoint"] ) : '';

				if ( $value !== '' ) 
				{
					$atts['class'] .= ' ' . sprintf( $format, 'pt', $value );
				}
			}

			if ( $widget->supports( 'padding_right' ) )
			{
				$value = isset( $data["pr_$breakpoint"] ) ? sanitize_html_class( $data["pr_$breakpoint"] ) : '';

				if ( $value !== '' ) 
				{
					$atts['class'] .= ' ' . sprintf( $format, 'pr', $value );
				}
			}

			if ( $widget->supports( 'padding_bottom' ) )
			{
				$value = isset( $data["pb_$breakpoint"] ) ? sanitize_html_class( $data["pb_$breakpoint"] ) : '';

				if ( $value !== '' ) 
				{
					$atts['class'] .= ' ' . sprintf( $format, 'pb', $value );
				}
			}

			if ( $widget->supports( 'padding_left' ) )
			{
				$value = isset( $data["pl_$breakpoint"] ) ? sanitize_html_class( $data["pl_$breakpoint"] ) : '';

				if ( $value !== '' ) 
				{
					$atts['class'] .= ' ' . sprintf( $format, 'pl', $value );
				}
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_Padding_Feature' );

