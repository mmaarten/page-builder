<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Row_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'row', __( 'Row' ), array
		(
			'description' => __( 'Displays a row.' ),
			'features'    => array
			( 
				'id', 
				'class', 
				'mt', 
				'mb', 
				'pt', 
				'pb', 
				'bg_color', 
				'bg_image', 
				'bg_overlay',
			),
		));

		// Layout
		$this->add_field( array
		(
			'key'           => "{$this->id}_layout",
			'name'          => 'layout',
			'label'         => __( 'Layout' ),
			'type'          => 'row_layout_picker',
			'default_value' => '',
		));

		add_action( 'pb/render_field/type=row_layout_picker', array( $this, 'layout_picker' ) );

		// Container
		$this->add_field( array
		(
			'key'           => "{$this->id}_container",
			'name'          => 'container',
			'label'         => __( 'Container' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'fixed' => __( 'Fixed width' ),
				'fluid' => __( 'Full width' ),
			),
			'default_value' => 'fixed',
		));

		// Align Items
		$this->add_field( array
		(
			'key'           => "{$this->id}_align_items",
			'name'          => 'align_items',
			'label'         => __( 'Align Items' ),
			'description'   => __( 'Set the vertical alignment for all columns inside this row.' ),
			'type'          => 'select',
			'choices'       => array
			(
				''         => PB_CHOICE_DONT_SET,
				'start'    => __( 'Start' ),
				'end'      => __( 'End' ),
				'center'   => __( 'Center' ),
				'baseline' => __( 'Baseline' ),
				'stretch'  => __( 'Stretch' ),
			),
			'default_value' => '',
		));

		// No Gutters
		$this->add_field( array
		(
			'key'           => "{$this->id}_gutters",
			'name'          => 'gutters',
			'label'         => __( 'Gutters' ),
			'description'   => __( 'Add horizontal spacing between columns.' ),
			'type'          => 'true_false',
			'default_value' => 1,
		));
	}

	public function layout_picker( $field )
	{
		$field_type = pb()->field_types->get_field( 'text' );

		// Presets

		$presets = apply_filters( 'pb/row_layout_presets', array
		( 
			'12', '6+6', '4+8', '8+4', '3+9', '9+3', '4+4+4', '3+6+3', '3+3+3+3' 
		));

		// Output

		?>

		<?php if ( $presets ) : ?>
		<p class="pb-layout-controls">
			<?php foreach ( $presets as $layout ): 
				$cols = explode( '+', $layout );
			?>
			<button type="button" class="button pb-layout-control" data-layout="<?php echo esc_attr( $layout ); ?>">
				<span class="pb-row">
					<?php foreach ( $cols as $col ) : ?>
					<span class="pb-col-<?php echo intval( $col ); ?>"></span>
					<?php endforeach; ?>
				</span>
			</button>
			<?php endforeach; ?>
		</p>

		<label class="pb-sub-label"><?php esc_html_e( 'Custom' ) ?></label>
		<?php endif; ?>

		<?php $field_type->render( $field ); ?>
		<p class="description"><?php esc_html_e( 'e.g. 1/2+1/3 creates 2 columns with 1/2 and 1/3 width.' ); ?></p>

		<?php
	}

	public function render( $args, $instance )
	{
		// Instance

		$instance = wp_parse_args( $instance, $this->get_defaults() );

		// Attributes

		$atts = array
		(
			'class' => 'row',
		);

		if ( $instance['align_items'] ) 
		{
			$atts['class'] .= " align-items-{$instance['align_items']}";
		}

		if ( ! $instance['gutters'] ) 
		{
			$atts['class'] .= ' no-gutters';
		}

		// Output

		echo $args['before'];

		?>

		<div class="container<?php echo $instance['container'] == 'fluid' ? '-fluid' : ''; ?>">
			<div<?php echo pb_esc_attr( $atts ); ?>>
				<?php pb()->widgets->the_child_widgets(); ?>
			</div>
		</div>

		<?php

		echo $args['after'];
	}

	public function available_widgets( $widgets )
	{
		// Only columns
		return array( 'column' => true );
	}
}

pb()->widgets->register_widget( 'PB_Row_Widget' );
