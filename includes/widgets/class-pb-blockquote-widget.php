<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Blockquote_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'blockquote', __( 'Blockquote' ), array
		(
			'description' => __( 'Displays a blockquote.' ),
			'features'    => array( 'id', 'class', 'text_align', 'margin_top', 'margin_bottom' )
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
			'tinymce'       => array
			(
				'toolbar1' => 'bold,italic,underline,undo,redo,link,fullscreen',
			    'toolbar2' => '',
			    'toolbar3' => '',
			    'toolbar4' => ''
			),
			'default_value' => 'Maxime commodi elit illum? Libero erat perspiciatis sodales? Dignissimos! Imperdiet, aut exercitationem alias malesuada, reiciendis mollis soluta dolorum. Aut. Nostrum harum, culpa, eros parturient natus diam quo do varius hymenaeos.',
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'footer',
			'name'          => 'footer',
			'title'         => __( 'Footer' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 20,
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
			'class' => 'blockquote' 
		);

		$atts = array_filter( $atts );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		?>

		<blockquote<?php echo pb_render_attributes( $atts ); ?>>

			<?php echo apply_filters( 'the_content', $instance['content'] ); ?>

			<?php if ( $instance['footer'] ) : ?>
			<footer class="blockquote-footer"><?php echo esc_html( $instance['footer'] ); ?></footer>
			<?php endif; ?>

		</blockquote>
		
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

pb()->widgets->register( 'PB_Blockquote_Widget' );
