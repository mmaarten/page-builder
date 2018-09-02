<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Heading_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'heading', __( 'Heading' ), array
		(
			'description' => __( 'Displays a heading.' ),
			'features'    => array( 'id', 'class', 'color', 'text_align', 'margin_top', 'margin_bottom' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		$this->add_field( array
		(
			'key'           => 'text',
			'name'          => 'text',
			'title'         => __( 'Text' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => __( 'Heading' ),
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'text_2',
			'name'          => 'text_2',
			'title'         => __( 'Secondary Text' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$this->add_field( array
		(
			'key'         => 'type',
			'name'        => 'type',
			'title'       => __( 'Type' ),
			'description' => '',
			'type'        => 'select',
			'choices'     => array
			(
				'h1' => __( 'Heading 1' ),
				'h2' => __( 'Heading 2' ),
				'h3' => __( 'Heading 3' ),
				'h4' => __( 'Heading 4' ),
				'h5' => __( 'Heading 5' ),
				'h6' => __( 'Heading 6' )
			),
			'default_value' => 'h2',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'         => 'display',
			'name'        => 'display',
			'title'       => __( 'Display' ),
			'description' => __( 'Use a larger, slightly more opinionated heading style.' ),
			'type'        => 'select',
			'choices'     => array
			(
				''  => PB_THEME_DEFAULTS,
				'1' => __( 'Display 1' ),
				'2' => __( 'Display 2' ),
				'3' => __( 'Display 3' ),
				'4' => __( 'Display 4' )
			),
			'default_value' => 'h2',
			'order'         => PB_ORDER_TAB_LAYOUT + 10
		));

		$this->add_field( array
		(
			'key'         => 'style',
			'name'        => 'style',
			'title'       => __( 'Style' ),
			'description' => '',
			'type'        => 'select',
			'choices'     => array
			(
				''   => PB_THEME_DEFAULTS,
				'1' => __( 'Heading 1' ),
				'2' => __( 'Heading 2' ),
				'3' => __( 'Heading 3' ),
				'4' => __( 'Heading 4' ),
				'5' => __( 'Heading 5' ),
				'6' => __( 'Heading 6' )
			),
			'default_value' => '',
			'preview'       => false,
			'order'         => PB_ORDER_TAB_LAYOUT + 20
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		/**
		 * Tag
		 * -----------------------------------------------------------
		 */

		if ( preg_match( '/^h\d{1,6}$/', $instance['type'] ) ) 
		{
			$tag = $instance['type'];
		}

		else
		{
			$tag = 'h2';
		}

		/**
		 * Attributes
		 * -----------------------------------------------------------
		 */

		$atts = array
		(
			'class' => ''
		);

		// Display

		if ( $instance['display'] ) 
		{
			$atts['class'] .= " display-{$instance['display']}";
		}

		// Style

		if ( $instance['style'] ) 
		{
			$atts['class'] .= " h{$instance['style']}";
		}

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		?>

		<<?php echo $tag . pb_render_attributes( $atts ); ?>>
			<?php echo esc_html( $instance['text'] ); ?>
			<?php if ( $instance['text_2'] ) : ?>
			<?php printf( ' <small>%s</small>', esc_html( $instance['text_2'] ) ); ?>
			<?php endif; ?>
		</<?php echo $tag; ?>>

		<?php
		
		echo $args['after_widget'];
	}
}

pb()->widgets->register( 'PB_Heading_Widget' );
