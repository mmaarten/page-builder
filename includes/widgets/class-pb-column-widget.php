<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Column_Widget extends PB_Widget
{
	public function get_choices( $context )
	{
		$choices['cols'] = array
		(
			'1'  => __( '1 column - 1/12' ),
			'2'  => __( '2 columns - 1/6' ),
			'3'  => __( '3 columns - 1/4' ),
			'4'  => __( '4 columns - 3/4' ),
			'5'  => __( '5 columns - 5/12' ),
			'6'  => __( '6 columns - 1/2' ),
			'7'  => __( '7 columns - 7/12' ),
			'8'  => __( '8 columns - 2/3' ),
			'9'  => __( '9 columns - 3/4' ),
			'10' => __( '10 columns - 5/6' ),
			'11' => __( '11 columns - 11/12' ),
			'12' => __( '12 columns - 1/1' ),
		);

		$choices['inherit'] = array
		(
			'' => __( 'Inherit from smaller.' ),
		) + $choices['cols'];

		$choices['dont_set'] = array
		(
			'' => __( "Don't set" ),
		) + $choices['cols'];

		if ( isset( $choices[ $context ] ) ) 
		{
			return $choices[ $context ];
		}

		return false;
	}

	public function __construct()
	{
		parent::__construct( 'column', __( 'Column' ), array
		(
			'description' => __( 'Displays a column.' ),
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_general",
			'name'          => 'general',
			'label'         => __( 'General' ),
			'type'          => 'tab',
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_cols",
			'name'          => 'cols',
			'label'         => __( 'Width' ),
			'type'          => 'select',
			'choices'       => $this->get_choices( 'cols' ),
			'default_value' => 12,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_tab_responsiveness",
			'name'          => 'tab_responsiveness',
			'label'         => __( 'Responsiveness' ),
			'type'          => 'tab',
		));

		$breakpoints = array( 'xs', 'sm', 'md', 'lg', 'xl' );

		$sub_fields = array();

		foreach ( $breakpoints as $breakpoint ) 
		{
			$choices = $this->get_choices( 'cols' );

			if ( $breakpoint == 'xs' ) 
			{
				$choices = $this->get_choices( 'dont_set' );
			}

			$sub_fields[] = array
			(
				'name'          => "tab_{$breakpoint}",
				'label'         => $breakpoint,
				'description'   => '',
				'type'          => 'tab',
				'default_value' => '',
			);

			$sub_fields[] = array
			(
				'name'          => "offset_{$breakpoint}",
				'label'         => __( 'Offset' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => $this->get_choices( 'dont_set' ),
				'default_value' => '',
			);

			if ( $breakpoint == 'sm' )
			{
				$sub_fields[] = array
				(
					'name'          => "cols_{$breakpoint}",
					'label'         => __( 'Width' ),
					'type'          => 'message',
					'message'       => __( 'Value from general width attribute.' ),
				);
			}

			else
			{
				$sub_fields[] = array
				(
					'name'          => "cols_{$breakpoint}",
					'label'         => __( 'Width' ),
					'description'   => '',
					'type'          => 'select',
					'choices'       => $choices,
					'default_value' => '',
				);
			}

			$sub_fields[] = array
			(
				'name'          => "order_{$breakpoint}",
				'label'         => __( 'Order' ),
				'description'   => '',
				'type'          => 'number',
				'default_value' => '',
			);

			$sub_fields[] = array
			(
				'name'          => "hidden_{$breakpoint}",
				'label'         => __( 'Hidden' ),
				'description'   => '',
				'type'          => 'true_false',
				'default_value' => 0,
			);
		}

		$this->add_field( array
		(
			'key'           => "{$this->id}_responsiveness",
			'name'          => 'responsiveness',
			'label'         => '',
			'description'   => __( 'Set options per screen size.' ),
			'type'          => 'group',
			'sub_fields'    => $sub_fields,
		));
	}
}

pb()->widgets->register_widget( 'PB_Column_Widget' );
