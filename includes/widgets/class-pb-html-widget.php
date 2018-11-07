<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_HTML_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'html', __( 'Custom HTML' ), array
		(
			'description' => __( 'Arbitrary HTML code.' ),
			'features'    => array( 'id', 'class' ),
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_content",
			'name'          => 'content',
			'label'         => __( 'HTML' ),
			'description'   => '',
			'type'          => 'textarea',
			'html'          => true,
			'default_value' => '',
		));
	}

	public function preview( $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$this->preview_meta( $instance );

		$preview_content = trim( $instance['content'] );

		?>

		<?php if ( $preview_content ) : ?>
		<div class="pb-preview-content">
			<?php echo esc_html( $preview_content ); ?>
		</div>
		<?php endif ?>

		<?php
	}

	public function render( $args, $instance )
	{
		// Instance

		$instance = wp_parse_args( $instance, $this->get_defaults() );

		// Output

		echo $args['before'];

		echo apply_filters( 'the_content', $instance['content'] );

		echo $args['after'];
	}
}

pb()->widgets->register_widget( 'PB_HTML_Widget' );
