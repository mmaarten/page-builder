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

		$content         = trim( $instance['content'] );
		$content         = wpautop( $content );
		$preview_content = strip_tags( $content, '<p><a><br><b><i><strong><em><h1><h2><h3><h4><h5><h6>' );

		?>

		<?php if ( $preview_content ) : ?>
		<div class="pb-preview-content">
			<?php echo $preview_content; ?>
		</div>
		<?php endif ?>

		<?php
	}
}

pb()->widgets->register_widget( 'PB_Text_Widget' );
