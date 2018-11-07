<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_BG_Overlay_Feature extends PB_Feature
{
	public function __construct()
	{
		parent::__construct( 'bg_overlay' );
	}

	public function widget( $widget )
	{
		if ( ! $widget->supports( $this->id ) ) 
		{
			return;
		}

		$wrapper = array( 'width' => 100 / 2 );

		$widget->add_field( array
		(
			'key'           => "{$widget->id}_bg_overlay",
			'name'          => 'bg_overlay',
			'label'         => __( 'Background Overlay' ),
			'description'   => '',
			'type'          => 'group',
			'sub_fields'    => array
			(
				array
				(
					'name'          => 'color',
					'label'         => __( 'Color' ),
					'description'   => '',
					'type'          => 'select',
					'choices'       => array
					(
						''            => PB_CHOICE_DONT_SET,
						'primary'     => __( 'Primary' ),
						'secondary'   => __( 'Secondary' ),
						'success'     => __( 'Success' ),
						'danger'      => __( 'Danger' ),
						'warning'     => __( 'Warning' ),
						'info'        => __( 'Info' ),
						'light'       => __( 'Light' ),
						'dark'        => __( 'Dark' ),
						'white'       => __( 'White' ),
						'transparent' => __( 'Transparent' ),
					),
					'default_value' => '',
					'wrapper'       => $wrapper,
				),

				array
				(
					'name'          => 'opacity',
					'label'         => __( 'Opacity' ),
					'description'   => '',
					'type'          => 'number',
					'default_value' => '',
					'wrapper'       => $wrapper,
				),
			),
			'category' => 'background',
			'order'    => 40,
		));
	}

	public function widget_html_attributes( $atts, $widget, $instance )
	{
		return $atts;
	}
}

pb()->features->register_feature( 'PB_BG_Overlay_Feature' );
