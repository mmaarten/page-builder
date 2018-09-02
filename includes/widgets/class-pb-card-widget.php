<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Card_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'card', __( 'Card' ), array
		(
			'description' => __( 'Displays a card.' ),
			'features'    => array( 'id', 'class', 'margin_top', 'margin_bottom' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'header',
			'name'          => 'header',
			'title'         => __( 'Header' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'title',
			'name'          => 'title',
			'title'         => __( 'Title' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$this->add_field( array
		(
			'key'           => 'image',
			'name'          => 'image',
			'title'         => __( 'Image' ),
			'description'   => '',
			'type'          => 'group',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));

		$this->add_field( array
		(
			'key'           => 'image_id',
			'name'          => 'id',
			'title'         => '',
			'description'   => '',
			'type'          => 'image',
			'default_value' => '',
			'order'         => 10,
			'parent'        => 'image'
		));

		$this->add_field( array
		(
			'key'           => 'image_size',
			'name'          => 'size',
			'title'         => __( 'Size' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => pb_get_image_size_choices(),
			'default_value' => 'large',
			'order'         => 20,
			'parent'        => 'image'
		));

		$this->add_field( array
		(
			'key'           => 'content',
			'name'          => 'content',
			'title'         => __( 'Content' ),
			'description'   => '',
			'type'          => 'editor',
			'default_value' => 'Consequuntur laboriosam beatae, consequat netus quos ut etiam illum voluptatum augue lobortis, totam tempus, nec! Purus mus laudantium, nobis in. Dictum totam consequat ipsum eum eligendi, deserunt ea eget! Modi.',
			'order'         => PB_ORDER_TAB_GENERAL + 40
		));

		$this->add_field( array
		(
			'key'           => 'buttons',
			'name'          => 'buttons',
			'title'         => __( 'Buttons' ),
			'description'   => '',
			'type'          => 'repeater',
			'default_value' => '',
			'add_row_text'  => __( 'Add Button' ),
			'order'         => PB_ORDER_TAB_GENERAL + 50
		));

		$this->add_field( array
		(
			'key'           => 'button_text',
			'name'          => 'text',
			'title'         => __( 'Text' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'order'         => 10,
			'parent'        => 'buttons'
		));

		$this->add_field( array
		(
			'key'           => 'button_type',
			'name'          => 'type',
			'title'         => __( 'Type' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				'' => PB_THEME_DEFAULTS
			), pb()->options->get( 'theme_colors' ) ),
			'default_value' => 'primary',
			'order'         => 20,
			'parent'        => 'buttons'
		));

		$this->add_field( array
		(
			'key'           => 'button_outline',
			'name'          => 'outline',
			'title'         => __( 'Outline' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'order'         => 30,
			'parent'        => 'buttons'
		));

		$this->add_field( array
		(
			'key'           => 'button_link',
			'name'          => 'link',
			'title'         => __( 'Link' ),
			'description'   => '',
			'type'          => 'url',
			'default_value' => '',
			'order'         => 40,
			'parent'        => 'buttons'
		));

		$this->add_field( array
		(
			'key'           => 'button_link_tab',
			'name'          => 'link_tab',
			'title'         => __( 'Open link in new window' ),
			'description'   => '',
			'type'          => 'true_false',
			'order'         => 50,
			'parent'        => 'button'
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

		?>

		<div class="card">

			<?php if ( $instance['header'] ) : ?>
			<div class="card-header"><?php echo esc_html( $instance['header'] ); ?></div>
			<?php endif; ?>

			<?php echo wp_get_attachment_image( $instance['image']['id'], $instance['image']['size'], false, array( 'class' => 'card-img-top' ) ); ?>
			
			<div class="card-body">

				<?php if ( $instance['title'] ) : ?>
				<h5 class="card-title"><?php echo esc_html( $instance['title'] ); ?></h5>
				<?php endif; ?>

				<?php if ( $instance['content'] ) : ?>
				<div class="card-text"><?php echo apply_filters( 'the_content', $instance['content'] ); ?></div>
				<?php endif; ?>

				<?php if ( is_array( $instance['buttons'] ) ) : ?>
				<?php foreach ( $instance['buttons'] as $button ) : ?>
				<?php pb_button( $button ); ?>
				<?php endforeach; ?>
				<?php endif; ?>
				
			</div>
		</div>
		
		<?php
		
		echo $args['after_widget'];
	}

	public function preview( $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$this->preview_meta( $instance );


		if ( $instance['content'] ) 
		{
			$content = wp_trim_words( strip_tags( $instance['content'] ), 10, '&hellip;' );
		}

		else
		{
			$content = '';
		}

		?>

		<div class="pb-widget-preview-content">

			<div class="pb-row">
				
				<?php if ( $instance['image']['id'] ) : ?>
				<div class="pb-col">
					<div class="pb-thumbnail">
						<?php echo wp_get_attachment_image( $instance['image']['id'], 'thumbnail' ); ?>
					</div>
				</div>
				<?php endif; ?>

				<div class="pb-col">

					<?php if ( $instance['title'] ) : ?>
					<h3><?php echo esc_html( $instance['title'] ); ?></h3>
					<?php endif; ?>

					<?php if ( $instance['content'] ) : ?>
					<?php echo wpautop( $content ); ?>
					<?php endif; ?>

					<?php if ( is_array( $instance['buttons'] ) ) : ?>
					<p>
						<?php foreach ( $instance['buttons'] as $button ) : ?>
						<a class="button"><?php echo esc_html( $button['text'] ); ?></a>
						<?php endforeach; ?>
					</p>
					<?php endif; ?>

				</div>
			</div>
		</div>

		<?php
	}
}

pb()->widgets->register( 'PB_Card_Widget' );
