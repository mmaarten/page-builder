<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Margin_Feature extends PB_Feature
{
	protected $choices     = array();
	protected $breakpoints = array();

	public function __construct()
	{
		parent::__construct( 'margin' );

		$this->choices = array_merge( array
		(
			''  => PB_THEME_DEFAULTS,
		), pb()->options->get( 'spacers' ));
	}

	public function widget_init( $widget )
	{
		$all_directions = array
		(
			'margin_top'    => array( 'slug' => 'mt', 'title' => __( 'Top' ) ),
			'margin_right'  => array( 'slug' => 'mr', 'title' => __( 'Right' ) ),
			'margin_bottom' => array( 'slug' => 'mb', 'title' => __( 'Bottom' ) ),
			'margin_left'   => array( 'slug' => 'ml', 'title' => __( 'Left' ) )
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
			'key'                   => 'margin',
			'name'                  => 'margin',
			'title'                 => __( 'Margin' ),
			'description'           => __( 'Set margin per screen size.' ),
			'description_placement' => 'label',
			'type'                  => 'group',
			'layout'                => 'row',
			'order'                 => PB_ORDER_TAB_SPACING + 10
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
				'key'           => "margin_tab_{$breakpoint}",
				'name'          => 'tab',
				'title'         => strtoupper( $breakpoint ),
				'description'   => '',
				'type'          => 'tab',
				'default_value' => '',
				'order'         => $order,
				'parent'        => 'margin'
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
					'parent'        => 'margin',
					'wrapper'       => array( 'width' => 100 / count( $directions ) )
				));
			}
		}
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		if ( ! $widget->supports( 'margin_top' )
			&& ! $widget->supports( 'margin_right' )
			&& ! $widget->supports( 'margin_bottom' )
			&& ! $widget->supports( 'margin_left' ) )
		{
			return $atts;
		}

		$data = isset( $instance['margin'] ) ? (array) $instance['margin'] : array();

		foreach ( pb()->options->get( 'grid_breakpoint_formats' ) as $breakpoint => $format ) 
		{
			if ( $widget->supports( 'margin_top' ) )
			{
				$value = isset( $data["mt_$breakpoint"] ) ? sanitize_html_class( $data["mt_$breakpoint"] ) : '';

				if ( $value !== '' ) 
				{
					$atts['class'] .= ' ' . sprintf( $format, 'mt', $value );
				}
			}

			if ( $widget->supports( 'margin_right' ) )
			{
				$value = isset( $data["mr_$breakpoint"] ) ? sanitize_html_class( $data["mr_$breakpoint"] ) : '';

				if ( $value !== '' ) 
				{
					$atts['class'] .= ' ' . sprintf( $format, 'mr', $value );
				}
			}

			if ( $widget->supports( 'margin_bottom' ) )
			{
				$value = isset( $data["mb_$breakpoint"] ) ? sanitize_html_class( $data["mb_$breakpoint"] ) : '';

				if ( $value !== '' ) 
				{
					$atts['class'] .= ' ' . sprintf( $format, 'mb', $value );
				}
			}

			if ( $widget->supports( 'margin_left' ) )
			{
				$value = isset( $data["ml_$breakpoint"] ) ? sanitize_html_class( $data["ml_$breakpoint"] ) : '';

				if ( $value !== '' ) 
				{
					$atts['class'] .= ' ' . sprintf( $format, 'ml', $value );
				}
			}
		}

		return $atts;
	}
}

pb()->features->register( 'PB_Margin_Feature' );

