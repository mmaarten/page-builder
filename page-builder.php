<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.
/*
Plugin Name:  Page Builder
Plugin URI:   
Description:  
Version:      0.3.1
Author:       Maarten Menten
Author URI:   https://profiles.wordpress.org/maartenm/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  page-builder
Domain Path:  /languages
*/

define( 'PB_FILE', __FILE__ );
define( 'PB_POST_TYPE_FEATURE', 'page-builder' );
define( 'PB_NONCE_NAME', 'pb_nonce' );

define( 'PB_THEME_DEFAULTS', __( '- Theme defaults -' ) );
define( 'PB_INHERIT_FROM_SMALLER', __( '- Inherit from smaller -' ) );

defined( 'PB_ORDER_TAB_GENERAL' ) 	 or define( 'PB_ORDER_TAB_GENERAL'	 , 0 );
defined( 'PB_ORDER_TAB_LAYOUT' ) 	 or define( 'PB_ORDER_TAB_LAYOUT'    , 1000 );
defined( 'PB_ORDER_TAB_BACKGROUND' ) or define( 'PB_ORDER_TAB_BACKGROUND', 2000 );
defined( 'PB_ORDER_TAB_SPACING' ) 	 or define( 'PB_ORDER_TAB_SPACING'	 , 3000 );
defined( 'PB_ORDER_TAB_ATTRIBUTES' ) or define( 'PB_ORDER_TAB_ATTRIBUTES', 4000 );
defined( 'PB_ORDER_TAB_SETTINGS' )   or define( 'PB_ORDER_TAB_SETTINGS'  , 9999 );

defined( 'PB_DEBUG' ) 				 or define( 'PB_DEBUG', false );
defined( 'PB_MAX_NUMBERPOSTS' ) 	 or define( 'PB_MAX_NUMBERPOSTS', 999 );

require_once plugin_dir_path( PB_FILE ) . 'includes/common.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/options.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/util.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/templates.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/supportable.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/icons.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/models.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/debug.php';

require_once plugin_dir_path( PB_FILE ) . 'includes/fields.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-field.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/field-types.php';

require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-feature.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/features.php';

require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-widget.php';
require_once plugin_dir_path( PB_FILE ) . 'includes/widgets.php';

if ( is_admin() ) 
{
	require_once plugin_dir_path( PB_FILE ) . 'includes/editor.php';
}

class PB
{
	public $version = null;

	public function __construct()
	{
		$this->version = '0.3.1';
	}

	public function init()
	{
		add_action( 'plugins_loaded', 	  array( $this, 'plugin_widgets_init' ) );
		add_action( 'init', 			  array( $this, 'post_widgets_init' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_scripts' ) );
		add_filter( 'body_class', 		  array( $this, 'body_class' ) );

		/**
		 * Options
		 * -----------------------------------------------------------
		 */

		$this->options->set( 'theme_colors', array
		(
			'primary'   => __( 'Primary' ),
			'secondary' => __( 'Secondary' ),
			'success'   => __( 'Success' ),
			'info'      => __( 'Info' ),
			'warning'   => __( 'Warning' ),
			'danger'    => __( 'Danger' ),
			'light'     => __( 'Light' ),
			'dark'      => __( 'Dark' )
		));

		$this->options->set( 'spacers', array
		(
			'0' => __( 'None' ),
			'1' => __( 'Extra Small' ),
			'2' => __( 'Small' ),
			'3' => __( 'Medium' ),
			'4' => __( 'Large' ),
			'5' => __( 'Extra Large' )
		));

		$this->options->set( 'opacities', array
		(
			'0'   => '0%',
			'5'   => '5%',
			'10'  => '10%',
			'15'  => '15%',
			'20'  => '20%',
			'25'  => '25%',
			'30'  => '30%',
			'35'  => '35%',
			'40'  => '40%',
			'45'  => '45%',
			'50'  => '50%',
			'55'  => '55%',
			'60'  => '60%',
			'65'  => '65%',
			'70'  => '70%',
			'75'  => '75%',
			'80'  => '80%',
			'85'  => '85%',
			'90'  => '90%',
			'95'  => '95%',
			'100' => '100%'
		));

		$this->options->set( 'text_align', array
		(
			'left'    => __( 'Left' ),
			'center'  => __( 'Center' ),
			'right'   => __( 'Right' ),
			'justify' => __( 'Justify' )
		));

		$this->options->set( 'text_transform', array
		(
			'lowercase'  => __( 'Lowercase' ),
			'uppercase'  => __( 'Uppercase' ),
			'capitalize' => __( 'Capitalize' )
		));

		$this->options->set( 'image_ratios', array
		(
			'21by9' => __( '21by9' ),
			'16by9' => __( '16by9' ),
			'4by3'  => __( '4by3' ),
			'1by1'  => __( '1by1' )
		));

		$this->options->set( 'embed_ratios', array
		(
			'21by9' => __( '21by9' ),
			'16by9' => __( '16by9' ),
			'4by3'  => __( '4by3' ),
			'1by1'  => __( '1by1' )
		));

		$this->options->set( 'grid_breakpoint_formats', array
		(
			'xs' => '%1$s-%2$d', 
			'sm' => '%1$s-sm-%2$d', 
			'md' => '%1$s-md-%2$d', 
			'lg' => '%1$s-lg-%2$d', 
			'xl' => '%1$s-xl-%2$d'
		));

		$this->options->set( 'grid_breakpoints', array
		(
			'xs' => 0,
			'sm' => 576,
			'md' => 768,
			'lg' => 992,
			'xl' => 1200
		));

		$this->options->set( 'columns', array
		(
			'1'  => __( '1/12 - 1 column' ),
			'2'  => __( '1/6 - 2 columns' ),
			'3'  => __( '1/4 - 3 columns' ),
			'4'  => __( '1/3 - 4 columns' ),
			'5'  => __( '5/12 - 5 columns' ),
			'6'  => __( '1/2 - 6 columns' ),
			'7'  => __( '7/12 - 7 columns' ),
			'8'  => __( '2/3 - 8 columns' ),
			'9'  => __( '3/4 - 9 columns' ),
			'10' => __( '5/6 - 10 columns' ),
			'11' => __( '11/12 - 11 columns' ),
			'12' => __( '1/1 - 12 columns' )
		));

		// Enables editor for pages

		add_post_type_support( 'page', PB_POST_TYPE_FEATURE );

		// Loads fields

		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-checkboxes-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-editor-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-group-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-image-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-message-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-number-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-repeater-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-select-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-tab-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-text-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-textarea-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-true-false-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-url-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-post-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-term-field.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/fields/class-pb-icon-field.php';

		// Loads features
		
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-bg-color-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-bg-image-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-bg-overlay-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-bg-position-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-bg-type-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-block-align-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-class-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-color-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-id-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-text-align-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-margin-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-padding-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-parallax-feature.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/features/class-pb-font-weight-feature.php';

		/**
		 * Loads widgets
		 * -----------------------------------------------------------
		 */

		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-row-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-column-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-heading-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-text-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-image-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-button-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-modal-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-map-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-youtube-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-gallery-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-carousel-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-icon-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-card-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-alert-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-list-widget.php';
		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-blockquote-widget.php';
	}

	public function post_widgets_init()
	{
		// Post Widget. Creates a widget per public post type.

		require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-post-widget.php';

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type ) 
		{
			if ( $post_type == 'attachment' ) 
			{
				continue;
			}

			$widget = new PB_Post_Widget( $post_type );

			pb()->widgets->register( $widget );
		}
	}

	public function plugin_widgets_init()
	{
		// Form Widget (Gravity Forms)

		if ( class_exists( 'GFAPI' ) ) 
		{
			require_once plugin_dir_path( PB_FILE ) . 'includes/widgets/class-pb-form-widget.php';
		}
	}

	public function register_scripts()
	{
		// Fancybox
		wp_register_style( 'jquery-fancybox', plugins_url( 'vendor/fancybox/dist/jquery.fancybox.min.css', PB_FILE ) , null, '3.3.5' );
		wp_register_script( 'jquery-fancybox', plugins_url( 'vendor/fancybox/dist/jquery.fancybox.min.js', PB_FILE ), array( 'jquery' ), '3.3.5' );

		// Owl Carousel
		wp_register_style( 'owl-carousel', plugins_url( 'vendor/owl.carousel/dist/assets/owl.carousel.min.css', PB_FILE ), null, '2.3.3' );
		wp_register_style( 'owl-carousel-theme', plugins_url( 'vendor/owl.carousel/dist/assets/owl.theme.default.min.css', PB_FILE ), null, '2.3.3' );
		wp_register_script( 'owl-carousel', plugins_url( 'vendor/owl.carousel/dist/owl.carousel.min.js', PB_FILE ), array( 'jquery' ), '2.3.3', true );

		// Core
		wp_register_style( 'pb-front', plugins_url( 'css/front.min.css', PB_FILE ) );
		wp_register_script( 'pb-front', plugins_url( 'js/dist/front.min.js', PB_FILE ), array( 'jquery' ), false, true );
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style( 'pb-front' );
		wp_enqueue_script( 'pb-front' );
	}

	public function maybe_enqueue_scripts()
	{
		if ( ! pb()->widgets->post_has_widgets() ) 
		{
			return;
		}

		$this->enqueue_scripts();
	}

	public function body_class( $classes )
	{
		/**
		 * General
		 * ---------------------------------------------------------------
		 */

		$classes[] = 'pb-no-js';

		/**
		 * Browser Info
		 * ---------------------------------------------------------------
		 */

		global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

		    if ( $is_lynx )     $classes[] = 'pb-browser-lynx';
		elseif ( $is_gecko ) 	$classes[] = 'pb-browser-gecko';
		elseif ( $is_opera ) 	$classes[] = 'pb-browser-opera';
		elseif ( $is_NS4 ) 		$classes[] = 'pb-browser-ns4';
		elseif ( $is_safari ) 	$classes[] = 'pb-browser-safari';
		elseif ( $is_chrome ) 	$classes[] = 'pb-browser-chrome';
		elseif ( $is_IE ) 		$classes[] = 'pb-browser-ie';
		else 					$classes[] = 'pb-browser-unknown';

		/* ------------------------------------------------------------ */

		return $classes;
	}
}

pb()->init();


