<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Column_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'column', __( 'Column' ), array
		(
			'description' => __( 'Displays a column.' ),
			'controls'    => array( 'add', 'edit', 'delete', 'toggle' ),
			'features'    => array
			( 
				'id', 
				'class',
				'margin_top', 
				'margin_bottom', 
				'padding_top', 
				'padding_bottom',
				'bg_image',
				'bg_image_size',
				'bg_position',
				'bg_type',
				'bg_color',
				'parallax',
				'bg_overlay'
			)
		));

		$choices = pb()->options->get( 'columns' );

		$offset_choices = array
		(
			'' => __( "Don't set" ),
			0  => __( 'No offset' )
		) + $choices;

		$order_choices = array
		(
			'' => __( "Don't set" ),
			1  => 1,
			2  => 2,
			3  => 3,
			4  => 4,
			5  => 5,
			6  => 6,
			7  => 7,
			8  => 8,
			9  => 9,
			10  => 10,
			11  => 11,
			12  => 12
		) + $choices;

		/**
		 * General
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'width',
			'name'          => 'width',
			'title'         => __( 'Width' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => $choices,
			'default_value' => 12,
			'order'         => PB_ORDER_TAB_GENERAL + 10 
		));

		// Responsiveness

		$this->add_field( array
		(
			'key'           => 'responsiveness',
			'name'          => 'responsiveness',
			'title'         => __( 'Responsiveness' ),
			'description'   => '',
			'type'          => 'group',
			'default_value' => '',
			'layout'        => 'block',
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$breakpoints = pb()->options->get( 'grid_breakpoint_formats' );

		$order = 0;

		foreach ( $breakpoints as $breakpoint => $format ) 
		{
			$_offset_choices = $offset_choices;
			$_width_choices  = $choices;
			$_order_choices  = $order_choices;

			if ( $breakpoint != 'xs' ) 
			{
				$_offset_choices = array
				(
					'' => PB_INHERIT_FROM_SMALLER,
				) + $_offset_choices;

				$_width_choices = array
				(
					'' => PB_INHERIT_FROM_SMALLER,
				) + $_width_choices;

				$_order_choices = array
				(
					'' => PB_INHERIT_FROM_SMALLER,
				) + $_order_choices;
			}

			else
			{
				$_width_choices = array
				(
					'' => __( "Don't set" ),
				) + $_width_choices;
			}

			$this->add_field( array
			(
				'key'           => "tab_{$breakpoint}",
				'name'          => 'tab',
				'title'         => strtoupper( $breakpoint ),
				'description'   => '',
				'type'          => 'tab',
				'default_value' => '',
				'parent'        => 'responsiveness',
				'order'         => $order
			));

			$this->add_field( array
			(
				'key'           => "offset_{$breakpoint}",
				'name'          => "offset_{$breakpoint}",
				'title'         => __( 'Offset' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => $_offset_choices,
				'default_value' => '',
				'parent'        => 'responsiveness',
				'order'         => $order + 10
			));

			if ( $breakpoint == 'md' ) 
			{
				$this->add_field( array
				(
					'key'           => "width_{$breakpoint}_message",
					'name'          => "width_{$breakpoint}_message",
					'title'         => __( 'Width' ),
					'description'   => '',
					'type'          => 'message',
					'message'       => __( 'Value from width attribute.' ),
					'default_value' => '',
					'parent'        => 'responsiveness',
					'order'         => $order + 20
				));
			}

			else
			{
				$this->add_field( array
				(
					'key'           => "width_{$breakpoint}",
					'name'          => "width_{$breakpoint}",
					'title'         => __( 'Width' ),
					'description'   => '',
					'type'          => 'select',
					'choices'       => $_width_choices,
					'default_value' => '',
					'parent'        => 'responsiveness',
					'order'         => $order + 20
				));
			}

			$this->add_field( array
			(
				'key'           => "order_{$breakpoint}",
				'name'          => "order_{$breakpoint}",
				'title'         => __( 'Order' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => $_order_choices,
				'default_value' => '',
				'parent'        => 'responsiveness',
				'order'         => $order + 30
			));

			$order += 40;
		}
	}

	public function widget_html_attributes( $atts, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		// Merges offset and width settings into one array

		$data = array_merge( (array) $instance['responsiveness'], array
		(
			'width_md' => $instance['width']
		));

		// Sets column classes

		$breakpoints = pb()->options->get( 'grid_breakpoint_formats' );

		foreach ( $breakpoints as $breakpoint => $format ) 
		{
			if ( isset( $data["offset_{$breakpoint}"] ) && $value = $data["offset_{$breakpoint}"] ) 
			{
				$atts['class'] .= ' ' . sprintf( $format, 'offset', $value );
			}

			if ( isset( $data["width_{$breakpoint}"] ) && $value = $data["width_{$breakpoint}"] ) 
			{
				$atts['class'] .= ' ' . sprintf( $format, 'col', $value );
			}

			if ( isset( $data["order_{$breakpoint}"] ) && $value = $data["order_{$breakpoint}"] ) 
			{
				$atts['class'] .= ' ' . sprintf( $format, 'order', $value );
			}
		}

		//

		return $atts;
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );
		
		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		pb()->widgets->the_child_widgets();

		echo $args['after_widget'];
	}
}

pb()->widgets->register( 'PB_Column_Widget' );
