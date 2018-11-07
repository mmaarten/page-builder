<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_URL_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'url' );
	}

	public function render( $field )
	{
		$atts = array
		(
			'type'  => 'text',
			'id'    => $field['id'],
			'name'  => $field['name'],
			'value' => $field['value'],
		);

		$atts = array_filter( $atts );

		echo '<input' . pb_esc_attr( $atts ) . '>';
	}

	public function sanitize( $value, $field )
	{
		return esc_url( $value );
	}

	public function translate( $value, $field )
	{
		if ( $value ) 
		{
			// Link
			$link = esc_url( $value );

			// Text
			$text = $link;

			// Remove site url
			$site_url = site_url();

			if ( stripos( $text, $site_url ) === 0 ) 
			{
				$text = substr( $text, strlen( $site_url ) );
			}

			// Remove protocol
			$text = preg_replace( '#^https?://#', '', $text );

			// Sanitize
			$text = trim( $text, '/' );

			// Return

			return sprintf( '<a href="%s" target="_blank">%s</a>', esc_attr( $link ), esc_html( $text ) );
		}

		return esc_html( $value );
	}
}

pb()->field_types->register_field( 'PB_URL_Field' );
