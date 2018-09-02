<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Form_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'form', __( 'Form' ), array
		(
			'description' => __( 'Displays a Gravity form.' ),
			'features'    => array
			( 
				'id', 
				'class', 
				'margin_top', 
				'margin_bottom'
			)
		));

		$form_choices = array();

		$forms = GFAPI::get_forms();

		foreach ( $forms as $form ) 
		{
			$form_choices[ $form['id'] ] = $form['title'];
		}

		/**
		 * General
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'         => 'form',
			'name'        => 'form',
			'title'       => __( 'Form' ),
			'description' => '',
			'type'        => 'select',
			'choices'     => $form_choices,
			'preview'     => true,
			'order'       => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'features',
			'name'          => 'features',
			'title'         => __( 'Features' ),
			'description'   => '',
			'type'          => 'checkboxes',
			'choices'       => array
			(
				'title'       => __( 'Display form title.' ),
				'description' => __( 'Display form description.' ),
				'ajax'        => __( 'Enable Ajax.' ),
			),
			'default_value' => array( 'title', 'description' ),
			'preview'       => false,
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$this->add_field( array
		(
			'key'           => 'tab_index',
			'name'          => 'tab_index',
			'title'         => __( 'Tabindex' ),
			'description'   => __( 'Specify the starting tab index for the fields of the form.' ),
			'type'          => 'number',
			'default_value' => '',
			'preview'       => false,
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		echo do_shortcode( sprintf( '[gravityform id="%s" title="%s" description="%s" ajax="%s" tabindex="%s"]',
			esc_attr( $instance['form'] ),
			in_array( 'title', $instance['features'] ) ? 'true' : 'false',
			in_array( 'description', $instance['features'] ) ? 'true' : 'false',
			in_array( 'ajax', $instance['features'] ) ? 'true' : 'false',
			esc_attr( $instance['tab_index'] ) ) );
		
		echo $args['after_widget'];
	}
}

pb()->widgets->register( 'PB_Form_Widget' );
