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

		list( $image_url ) = wp_get_attachment_image_src( $field['value'], 'thumbnail' );

		?>

		<div class="pb-media-picker">

			<?php echo $input; ?>

			<?php pb_image_preview( $image_url ); ?>

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
