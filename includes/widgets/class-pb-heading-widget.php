<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Heading_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'heading', __( 'Heading' ), array
		(
			'description' => __( 'Displays a heading.' ),
			'features'    => array( 'id', 'class', 'color', 'text_align', 'font_weight', 'mt', 'mr', 'mb', 'ml', 'pt', 'pr', 'pb', 'pl' ),
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_text",
			'name'          => 'text',
			'label'         => __( 'Text' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => __( 'Heading' ),
			'preview'       => true,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_text_2",
			'name'          => 'text_2',
			'label'         => __( 'Secondary Text' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'preview'       => true,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_type",
			'name'          => 'type',
			'label'         => __( 'Type' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'h1'    => __( 'Heading 1' ),
				'h2'    => __( 'Heading 2' ),
				'h3'    => __( 'Heading 3' ),
				'h4'    => __( 'Heading 4' ),
				'h5'    => __( 'Heading 5' ),
				'h6'    => __( 'Heading 6' ),
			),
			'default_value' => 'h2',
			'preview'       => true,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_display",
			'name'          => 'display',
			'label'         => __( 'Display' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'' => PB_CHOICE_DONT_SET,
				'1'    => 1,
				'2'    => 2,
				'3'    => 3,
				'4'    => 4,
			),
			'default_value' => 'h2',
			'category'      => 'layout'
		));
	}

	public function render( $args, $instance )
	{
		// Instance

		$defaults = $this->get_defaults();
		$instance = wp_parse_args( $instance, $defaults );

		// Tag

		if ( preg_match( '/^h{1,6}$/', $instance['type'] ) ) 
		{
			$tag = $instance['type'];
		}

		else
		{
			$tag = $defaults['type'];
		}

		// Attributes

		$atts = array();

		if ( $instance['display'] ) 
		{
			$atts['class'] = "display-{$instance['display']}";
		}

		// Output

		echo $args['before'];

		printf( '<%s%s>', $tag, pb_esc_attr( $atts ) );

		echo esc_html( $instance['text'] );

		if ( $instance['text_2'] ) 
		{
			printf( ' <small>%s</small>', esc_html( $instance['text_2'] ) );
		}

		printf( '</%s>', $tag );

		echo $args['after'];
	}
}

pb()->widgets->register_widget( 'PB_Heading_Widget' );
