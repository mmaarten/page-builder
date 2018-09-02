<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Icon_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'icon' );

		add_action( 'wp_ajax_pb_icon_picker', array( $this, 'picker' ) );
	}

	public function prepare( $field )
	{
		$icon = pb()->icons->get_icon_html( $field['value'] );

		if ( $icon ) 
		{
			$field['wrapper']['class'] .= ' pb-has-value';
		}

		return $field;
	}

	public function render( $field )
	{
		$icon = pb()->icons->get_icon_html( $field['value'] );

		// Attributes

		$atts = array
		(
			'type'  => 'hidden',
			'id'    => $field['id'],
			'name'  => $field['name'],
			'value' => $field['value']
		);

		$atts = array_filter( $atts );

		//

		printf( '<input%s>', pb_render_attributes( $atts ) );

		?>

		<div class="pb-thumbnail pb-thumbnail-sm pb-show-if-value">
			<div class="pb-thumbnail-item pb-icon-preview">
				<?php echo $icon; ?>
			</div>
		</div>

		<p class="pb-hide-if-value">
			<button type="button" class="button pb-icon-set"><?php esc_html_e( 'Select' ); ?></button>
		</p>

		<p class="pb-show-if-value">
			<button type="button" class="button pb-icon-unset"><?php esc_html_e( 'Remove' ); ?></button>
		</p>

		<?php
	}

	public function sanitize( $value, $field )
	{
		if ( $value && pb()->icons->get_icon( $value ) ) 
		{
			return $value;
		}

		return '';
	}

	public function translate( $value, $field )
	{
		return pb()->icons->get_icon_html( $value );
	}

	public function picker()
	{
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		//

		$active = isset( $_POST['active'] ) ? $_POST['active'] : '';

		$icons = pb()->icons->get_icons();

		?>

		<div id="pb-icon-picker">

			<h1><?php esc_html_e( 'Available Icons' ); ?></h1>

			<?php if ( is_wp_error( $icons ) ) : 
				$message = sprintf( __( 'Unable to get icons: %s' ), $icons->get_error_message() );
			?>
			<?php pb_notice( $message, 'error' ); ?>
			<?php else : ?>

			<p>
				<input type="search" class="widefat pb-icon-picker-search" placeholder="<?php esc_attr_e( 'Search…' ); ?>">
			</p>

			<p class="pb-icons">

				<?php foreach ( $icons as $icon ) : 
					
					$class = $icon['id'] == $active ? ' active' : '';

					$term = str_replace( '-', ' ', $icon['id'] );

					$html = pb()->icons->get_icon_html( $icon['id'] );
				?>
				<button type="button" class="button pb-icon-picker-icon<?php echo $class; ?>" title="<?php echo esc_attr( $icon['id'] ); ?>" data-id="<?php echo esc_attr( $icon['id'] ); ?>" data-term="<?php echo esc_attr( $term ); ?>"><?php echo $html; ?></button>
				<?php endforeach; ?>
			</p>

			<?php endif; ?>

		</div>

		<?php

		wp_die();
	}
}

pb()->field_types->register( 'PB_Icon_Field' );