<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Debug
{
	public function __construct()
	{
		if ( PB_DEBUG ) 
		{
			add_filter( 'script_loader_src', array( $this, 'script_loader_src' ), 10, 2 );
			add_filter( 'style_loader_src' , array( $this, 'script_loader_src' ), 10, 2 );
			add_action( 'admin_body_class' , array( $this, 'body_class' ) );
		}
	}

	public function script_loader_src( $src, $handle )
	{
		// Checks handle prefix

		if ( strpos( $handle, 'pb-' ) !== 0 ) 
		{
			return $src;
		}

		// Loads unminified version (if available)

		$base_url  = plugins_url( '/', PB_FILE );
		$base_path = plugin_dir_path( PB_FILE );

		if ( stripos( $src, $base_url ) === 0 ) 
		{
			// removes 'min'
			$src_2 = preg_replace( '/\.min\.(js|css)/', '.$1', $src );

			// Checks if file exists
			$file = $base_path . substr( $src_2, strlen( $base_url ) );
			$file = preg_replace( '/\?.*/', '', $file ); // removes query string

			if ( file_exists( $file ) ) 
			{
				return $src_2;
			}
		}

		return $src;
	}

	public function body_class( $class )
	{
		$class .= ' pb-debug';

		return trim( $class );
	}
}

pb()->debug = new PB_Debug();
