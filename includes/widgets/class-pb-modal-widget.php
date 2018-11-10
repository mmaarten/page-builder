<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Modal_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'modal', __( 'Modal' ), array
		(
			'description' => __( 'Displays a modal.' ),
			'features'    => array(),
		));

		// General

		$this->add_field( array
		(
			'key'           => "{$this->id}_modal_id",
			'name'          => 'modal_id',
			'label'         => __( 'Modal ID' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'preview'       => true,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_title",
			'name'          => 'title',
			'label'         => __( 'Title' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_body",
			'name'          => 'body',
			'label'         => __( 'Body' ),
			'description'   => '',
			'type'          => 'editor',
			'default_value' => '',
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_center_vertically",
			'name'          => 'center_vertically',
			'label'         => __( 'Center Vertically' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'category'      => 'layout',
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_size",
			'name'          => 'size',
			'label'         => __( 'Size' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'sm' => __( 'Small' ),
				'md' => __( 'Medium' ),
				'lg' => __( 'Large' ),
			),
			'default_value' => 'md',
			'category'      => 'layout',
		));
	}

	public function render( $args, $instance )
	{
		// Instance

		$instance = wp_parse_args( $instance, $this->get_defaults() );

		// Attributes

		$atts = array
		(
			'class'           => 'modal fade',
			'tabindex'        => '-1' ,
			'role'            => 'dialog' ,
			'aria-hidden'     => 'true',
		);

		if ( $instance['modal_id'] ) 
		{
			$atts['id'] = $instance['modal_id'];

			$atts['aria-labelledby'] = "{$instance['modal_id']}-title";
		}

		// Dialog

		$dialog = array
		( 
			'class' => 'modal-dialog',
			'role'  => 'document',
		);

		if ( $instance['size'] ) 
		{
			$dialog['class'] .= " modal-{$instance['size']}";
		}

		if ( $instance['center_vertically'] ) 
		{
			$dialog['class'] .= ' modal-dialog-centered';
		}

		// Output

		echo $args['before'];

		?>

		<div<?php echo pb_esc_attr( $atts ); ?>>
			<div<?php echo pb_esc_attr( $dialog ); ?>>
				<div class="modal-content">
					<div class="modal-header">
						<?php if ( $instance['title'] ) : ?>
						<h5 class="modal-title" id="<?php echo $instance['modal_id']; ?>-title"><?php echo esc_html( $instance['title'] ); ?></h5>
						<?php endif; ?>
						
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?php echo apply_filters( 'the_content', $instance['body'] ); ?>
					</div>
				</div>
			</div>
		</div>

		<?php

		echo $args['after'];
	}
}

pb()->widgets->register_widget( 'PB_Modal_Widget' );
