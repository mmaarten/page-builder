<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Icons
{
	const TRANSIENT = 'pb_icons';

	public function __construct()
	{
		
	}

	public function get_icon_html( $id, $atts = array() )
	{
		$icon = $this->get_icon( $id );

		if ( ! $icon ) 
		{
			return '';
		}

		return sprintf( '<span class="%s" aria-hidden="true"></span>', esc_attr( $icon['class'] ) );
	}

	public function get_icon( $id )
	{
		$icons = $this->get_icons();

		if ( ! is_wp_error( $icons ) && isset( $icons[ $id ] ) ) 
		{
			return $icons[ $id ];
		}

		return null;
	}

	public function get_icons()
	{
		// Checks cache

		$icons = get_transient( self::TRANSIENT );

		if ( is_array( $icons ) ) 
		{
			return $icons;
		}

		// Reads json file

		$file = plugin_dir_path( PB_FILE ) . 'json/icons.json';

		$contents = file_get_contents( $file );

		if ( $contents === false ) 
		{
			return new WP_Error( 'file_get_contents', __( 'Unable to get file contents.' ) );
		}

		$icons = json_decode( $contents, true );

		if ( ( $error = json_last_error() ) != JSON_ERROR_NONE ) 
		{
			return new WP_Error( 'json', sprintf( __( 'Unable parse json: %s' ), $error ) );
		}

		// Saves icons to cache

		set_transient( self::TRANSIENT, $icons );
	
		//

		return $icons;
	}
}

pb()->icons = new PB_Icons();

