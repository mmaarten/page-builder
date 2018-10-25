<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.
/*
Plugin Name: Page Builder
Plugin URI:
Description:
Version: 0.1.0
Author: Maarten Menten
Author URI: https://profiles.wordpress.org/maartenm
Text Domain: page-builder
Domain Path: /languages
*/

define( 'PB_FILE'             , __FILE__ );
define( 'PB_NONCE_NAME'       , 'pbnonce' );
define( 'PB_POST_TYPE_FEATURE', 'page-builder' );

require_once plugin_dir_path( PB_FILE ) . 'includes/common.php';

require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-widget.php';

require_once plugin_dir_path( PB_FILE ) . 'includes/field-types.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/editor.php';

// Widgets
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-button-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-column-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-row-widget.php';

class PB
{
	public function __construct()
	{
		// Enable page builder for pages.
		add_post_type_support( 'page', PB_POST_TYPE_FEATURE );
	}
}
