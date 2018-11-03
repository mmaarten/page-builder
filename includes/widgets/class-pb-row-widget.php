<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Row_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'row', __( 'Row' ), array
		(
			'description' => __( 'Displays a row.' ),
			'features'    => array( 'id', 'class', 'mt', 'mb', 'pt', 'pb' ),
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_layout",
			'name'          => 'layout',
			'label'         => __( 'Layout' ),
			'type'          => 'row_layout_picker',
			'default_value' => '',
		));

		add_action( 'pb/render_field/type=row_layout_picker', array( $this, 'layout_picker' ) );
	}

	public function layout_picker( $field )
	{
		$field_type = pb()->field_types->get_field( 'text' );

		// Presets

		$presets = array( '12', '6+6', '4+8', '8+4', '3+9', '9+3', '4+4+4', '3+6+3', '3+3+3+3' );

		// Output

		?>

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

		<?php $field_type->render( $field ); ?>

		<?php
	}
}

pb()->widgets->register_widget( 'PB_Row_Widget' );
