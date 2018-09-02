<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Templates
{
	public function __construct()
	{
			
	}

	public function locate( $template_names, $load = false, $require_once = true )
	{
		$located = '';

		foreach ( (array) $template_names as $template_name ) 
		{
			// Theme

			$located = locate_template( "page-builder/templates/$template_name", false );

			if ( $located ) 
			{
				break;
			}

			// Plugin

			$file = plugin_dir_path( PB_FILE ) . "templates/$template_name";

			if ( file_exists( $file ) ) 
			{
				$located = $file;

				break;
			}
		}

		if ( $load && $located != '' ) 
		{
			load_template( $located, $require_once );
		}

		return $located;
	}
}

pb()->templates = new PB_Templates();
