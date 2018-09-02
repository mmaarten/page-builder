<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Alert_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'alert', __( 'Alert' ), array
		(
			'description' => __( 'Displays an alert.' ),
			'features'    => array( 'id', 'class', 'margin_top', 'margin_bottom' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		$this->add_field( array
		(
			'key'           => 'heading',
			'name'          => 'heading',
			'title'         => __( 'Heading' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 10,
			'preview'       => true
		));

		$this->add_field( array
		(
			'key'           => 'content',
			'name'          => 'content',
			'title'         => __( 'Content' ),
			'description'   => '',
			'type'          => 'editor',
			'default_value' => 'Consequuntur laboriosam beatae, consequat netus quos ut etiam illum voluptatum augue lobortis, totam tempus, nec! Purus mus laudantium, nobis in. Dictum totam consequat ipsum eum eligendi, deserunt ea eget! Modi.',
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'type',
			'name'          => 'type',
			'title'         => __( 'Type' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				'' => PB_THEME_DEFAULTS
			), pb()->options->get( 'theme_colors' ) ),
			'default_value' => 'primary',
			'order'         => PB_ORDER_TAB_LAYOUT + 10,
			'preview'       => true
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
			'class' => 'alert',
			'role'  => 'alert' 
		);

		// Type

		if ( $instance['type'] )
		{
			 $atts['class'] .= " alert-{$instance['type']}";
		}

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		?>

		<div<?php echo pb_render_attributes( $atts ); ?>>
			<?php if ( $instance['heading'] ) : ?>
			<h4 class="alert-heading"><?php echo esc_html( $instance['heading'] ); ?></h4>
			<?php endif; ?>
			<?php echo apply_filters( 'the_content', $instance['content'] ); ?>
		</div>
		
		<?php
		
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

pb()->widgets->register( 'PB_Alert_Widget' );
