<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Image_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'image' );
	}

	public function field( $field )
	{
		return $field;
	}

	public function prepare( $field )
	{
		return $field;
	}

	public function render( $field )
	{
		$atts = array
		(
			'type'  => 'hidden',
			'id'    => $field['id'],
			'class' => 'pb-media-picker-input',
			'name'  => $field['name'],
			'value' => $field['value'],
		);

		$atts = array_filter( $atts );

		$input = '<input' . pb_esc_attr( $atts ) . '>';

		list( $image_url ) = wp_get_attachment_image_src( $field['value'] );

		?>

		<div class="pb-media-picker">

			<?php echo $input; ?>

			<div class="wp-core-ui">
				<div class="attachment" style="float:none; padding: 0;">
					<div class="attachment-preview">
						<div class="thumbnail">
							<div class="centered">
								<img class="pb-media-picker-image" src="<?php echo esc_url( $image_url ); ?>">
							</div>
						</div>
					</div>
				</div>
			</div>

			<p class="pb-hide-if-value">
				<button type="button" class="button pb-media-picker-add"><?php esc_html_e( 'Select Image' ); ?></button>
			</p>

			<p class="pb-show-if-value">
				<button type="button" class="button pb-media-picker-remove"><?php esc_html_e( 'Remove Image' ); ?></button>
			</p>

		</div>

		<?php
	}

	public function sanitize( $value, $field )
	{
		if ( $value && get_post_type( $value ) == 'attachment' ) 
		{
			return $value;
		}

		return 0;
	}

	public function enqueue_scripts()
    {
        wp_enqueue_media();
    }
}

pb()->field_types->register_field( 'PB_Image_Field' );
