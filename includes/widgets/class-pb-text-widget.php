<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Text_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'text', __( 'Text' ), array
		(
			'description' => __( 'Arbitrary text.' ),
			'features'    => array
			( 
				'id', 
				'class', 
				'color',
				'bg_color',
				'margin_top', 
				'margin_right', 
				'margin_bottom', 
				'margin_left',
				'padding_top', 
				'padding_right', 
				'padding_bottom', 
				'padding_left'
			)
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'content',
			'name'          => 'content',
			'title'         => __( 'Content' ),
			'description'   => '',
			'type'          => 'editor',
			'default_value' => 'Consequuntur laboriosam beatae, consequat netus quos ut etiam illum voluptatum augue lobortis, totam tempus, nec! Purus mus laudantium, nobis in. Dictum totam consequat ipsum eum eligendi, deserunt ea eget! Modi.',
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'lead',
			'name'          => 'lead',
			'title'         => __( 'Lead' ),
			'description'   => __( 'Make text stand out.' ),
			'type'          => 'true_false',
			'default_value' => 0,
			'order'         => PB_ORDER_TAB_LAYOUT + 10
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		/**
		 * Attributes
		 * -----------------------------------------------------------
		 */

		$atts = array
		(
			'class' => ''
		);

		if ( $instance['lead'] ) 
		{
			$atts['class'] .= 'lead';
		}

		// Removes empty attributes

		$atts = array_filter( $atts );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		if ( count( $atts ) ) 
		{
			printf( '<div%s>', pb_render_attributes( $atts ) );
		}

		echo apply_filters( 'the_content', $instance['content'] );

		if ( count( $atts ) ) 
		{
			echo '</div>';
		}

		echo $args['after_widget'];
	}

	public function preview( $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$this->preview_meta( $instance );

		if ( ! $instance['content'] ) 
		{
			return;
		}

		printf( '<div class="pb-widget-preview-content">%s</div>', wpautop( $instance['content'] ) );
	}
}

pb()->widgets->register( 'PB_Text_Widget' );
