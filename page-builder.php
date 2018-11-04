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

define( 'PB_FILE'      , __FILE__ );
define( 'PB_NONCE_NAME', 'pbnonce' );
define( 'PB_POST_TYPE_FEATURE', 'page-builder' );
define( 'PB_CHOICE_DONT_SET', __( "Don't set" ) );
define( 'PB_CHOICE_INHERIT' , __( 'Inherit from smaller' ) );

class PB 
{
	public function __construct()
	{
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init()
	{
		// Enable page builder for pages.
		add_post_type_support( 'page', PB_POST_TYPE_FEATURE );
	}
}

require_once plugin_dir_path( PB_FILE ) . 'includes/common.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/editor.php';

require_once plugin_dir_path( PB_FILE ) . 'includes/class-pb-supportable.php';

// Field types
require_once plugin_dir_path( PB_FILE ) . 'includes/field-types.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-group-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-editor-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-image-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-message-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-number-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-select-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-text-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-textarea-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-true_false-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-url-field.php';

// Features
require_once plugin_dir_path( PB_FILE ) . 'includes/features.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-bg_color-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-bg_image-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-bg_overlay-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-block_align-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-class-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-color-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-font_weight-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-id-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-margin-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-padding-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-text_align-feature.php';

// Widgets
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-button-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-column-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-heading-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-html-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-image-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-row-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-text-widget.php';
