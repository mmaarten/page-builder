<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Modal_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'modal', __( 'Modal' ), array
		(
			'description' => __( 'Displays a modal.' ),
			'features'    => array( 'id', 'class' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		$this->add_field( array
		(
			'key'           => 'modal_id',
			'name'          => 'modal_id',
			'title'         => __( 'Modal ID' ),
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
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$this->add_field( array
		(
			'key'           => 'content',
			'name'          => 'content',
			'title'         => __( 'Content' ),
			'description'   => '',
			'type'          => 'editor',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'size',
			'name'          => 'size',
			'title'         => __( 'Size' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'sm' => __( 'Small' ),
				'md' => __( 'Medium' ),
				'lg' => __( 'Large' ),
			),
			'default_value' => 'md',
			'order'         => PB_ORDER_TAB_LAYOUT + 10
		));

		$this->add_field( array
		(
			'key'           => 'center',
			'name'          => 'center',
			'title'         => __( 'Center Vertically' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'order'         => PB_ORDER_TAB_LAYOUT + 20
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );
		
		echo $args['before_widget'];

		?>

		<div class="modal fade" id="<?php echo esc_attr( $instance['modal_id'] ); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo esc_attr( $instance['modal_id'] ); ?>-title" aria-hidden="true">
			<div class="modal-dialog modal-<?php echo esc_attr( $instance['size'] ); ?><?php echo $instance['center'] ? ' modal-dialog-centered' : ''; ?>" role="document">
				<div class="modal-content">

					<div class="modal-header">

						<?php if ( $instance['title'] ) : ?>
						<h5 class="modal-title" id="<?php echo esc_attr( $instance['modal_id'] ); ?>-title"><?php echo esc_html( $instance['title'] ); ?></h5>
						<?php endif ?>
						
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>

					</div>

					<div class="modal-body">
						<?php echo apply_filters( 'the_content', $instance['content'] ); ?>
					</div>

				</div><!-- .modal-content -->
			</div><!-- .modal-dialog -->
		</div><!-- .modal -->

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

pb()->widgets->register( 'PB_Modal_Widget' );
