<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Text_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'text', __( 'Text' ), array
		(
			'description' => __( 'Arbitrary text.' ),
			'features'    => array( 'id', 'class', 'mt', 'mr', 'mb', 'ml', 'pt', 'pr', 'pb', 'pl' ),
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_content",
			'name'          => 'content',
			'label'         => __( 'Content' ),
			'description'   => '',
			'type'          => 'editor',
			'default_value' => 'Erat ipsa magni pariatur modi? Vel porro odit laboris, officiis dolorem, sequi, nemo volutpat dolorum, molestias! Eos laboris? Orci magni porro ea labore velit, class elit totam pede pellentesque non.',
		));
	}

	public function preview( $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$this->preview_meta( $instance );

		$content = trim( $instance['content'] );
		$content = wpautop( $content );

		// Strip tags

		$allowable_tags = array
		( 
			'<p>'      => true,
			'<br>'     => true,
			'<a>'      => true,
			'<em>'     => true,
			'<strong>' => true,
			'<h1>'     => true,
			'<h2>'     => true,
			'<h3>'     => true,
			'<h4>'     => true,
			'<h5>'     => true,
			'<h6>'     => true,
		);

		$allowable_tags = apply_filters( 'pb/widget_text_widget_preview_content_allowable_tags', $allowable_tags );
		$allowable_tags = implode( '', array_keys( $allowable_tags ) );

		$preview_content = strip_tags( $content, $allowable_tags );

		// Output

		?>

		<?php if ( $preview_content ) : ?>
		<div class="pb-preview-content">
			<?php echo $preview_content; ?>
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

pb()->widgets->register_widget( 'PB_Text_Widget' );
