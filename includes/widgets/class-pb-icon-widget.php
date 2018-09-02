<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Icon_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'icon', __( 'Icon' ), array
		(
			'description' => __( 'Displays an icon.' ),
			'features'    => array( 'id', 'class', 'text_align', 'margin_top', 'margin_bottom' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'icon',
			'name'          => 'icon',
			'title'         => __( 'Icon' ),
			'description'   => '',
			'type'          => 'icon',
			'default_value' => 'flask',
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'size',
			'name'          => 'size',
			'title'         => __( 'Size' ),
			'description'   => __( 'Related to the font size.' ),
			'type'          => 'select',
			'choices'       => array
			(
				''   => PB_THEME_DEFAULTS,
				'1'  => __( '1x' ),
				'2'  => __( '2x' ),
				'3'  => __( '3x' ),
				'4'  => __( '4x' ),
				'5'  => __( '5x' ),
				'6'  => __( '6x' ),
				'7'  => __( '7x' ),
				'8'  => __( '8x' ),
				'9'  => __( '9x' ),
				'10' => __( '10x' )
			),
			'default_value' => '3',
			'preview' 	    => true,
			'order'         => PB_ORDER_TAB_LAYOUT + 10
		));

		$this->add_field( array
		(
			'key'           => 'color',
			'name'          => 'color',
			'title'         => __( 'Color' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				'' => PB_THEME_DEFAULTS,
			), pb()->options->get( 'theme_colors' ) ),
			'default_value' => '',
			'preview' 	    => true,
			'order'         => PB_ORDER_TAB_LAYOUT + 20
		));

		$this->add_field( array
		(
			'key'           => 'bg_color',
			'name'          => 'bg_color',
			'title'         => __( 'Background Color' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				'' => PB_THEME_DEFAULTS,
			), pb()->options->get( 'theme_colors' ) ),
			'default_value' => '',
			'preview' 	    => true,
			'order'         => PB_ORDER_TAB_LAYOUT + 30
		));

		$this->add_field( array
		(
			'key'           => 'shape',
			'name'          => 'shape',
			'title'         => __( 'Background Shape' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				''          => PB_THEME_DEFAULTS,
				'rounded'   => __( 'Rounded' ),
				'circle'    => __( 'Circle' )
			),
			'default_value' => '',
			'preview' 	    => true,
			'order'         => PB_ORDER_TAB_LAYOUT + 40
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$icon = pb()->icons->get_icon( $instance['icon'] );

		if ( ! $icon ) 
		{
			return;
		}

		/**
		 * Attributes
		 * -----------------------------------------------------------
		 */

		$atts = array
		( 
			'class' => $icon['class']
		);

		$atts['class'] .= " d-inline-block";

		// Size

		if ( $instance['size'] ) 
		{
			$atts['class'] .= " pb-icon-{$instance['size']}x";
		}

		// Shape

		if ( $instance['shape'] == 'rounded' )
		{
			$atts['class'] .= ' rounded';
		}

		elseif ( $instance['shape'] == 'circle' )
		{
			$atts['class'] .= ' rounded-circle';
		}

		// Color

		if ( $instance['color'] )
		{
			$atts['class'] .= " text-{$instance['color']}";
		}

		// Background Color

		if ( $instance['bg_color'] )
		{
			$atts['class'] .= " bg-{$instance['bg_color']}";
		}

		$atts = array_filter( $atts );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		?>

		<span<?php echo pb_render_attributes( $atts ); ?> aria-hidden="true"></span>
		
		<?php
		
		echo $args['after_widget'];
	}

	public function preview( $instance )
	{
		$this->preview_meta( $instance );

		//

		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$icon = pb()->icons->get_icon_html( $instance['icon'] );

		if ( ! $icon ) 
		{
			pb_notice( __( 'No icon set' ) );

			return;
		}

		?>

		<div class="pb-widget-preview-content">

			<div class="pb-thumbnail pb-thumbnail-sm">
				<div class="pb-thumbnail-item">
					<?php echo $icon; ?>
				</div>
			</div>

		</div>

		<?php
	}
}

pb()->widgets->register( 'PB_Icon_Widget' );
