<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Button_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'button', __( 'Button' ), array
		(
			'description' => __( 'Displays a button.' ),
			'features'    => array( 'id', 'class', 'text_align', 'margin_top', 'margin_bottom' )
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
			'default_value' => __( 'Button' ),
			'preview'       => 1,
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'link',
			'name'          => 'link',
			'title'         => __( 'Link' ),
			'description'   => '',
			'type'          => 'url',
			'default_value' => '',
			'preview'       => 1,
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$this->add_field( array
		(
			'key'           => 'link_tab',
			'name'          => 'link_tab',
			'title'         => __( 'Open link in new window' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));

		$this->add_field( array
		(
			'key'           => 'behavior',
			'name'          => 'behavior',
			'title'         => __( 'Behavior' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				''         => __( 'Link' ),
				'modal'    => __( 'Toggle Modal' ),
				'collapse' => __( 'Expand/collapse' )
			),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 40
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'type',
			'name'          => 'type',
			'title'         => __( 'Type' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				'' => PB_THEME_DEFAULTS
			), pb()->options->get( 'theme_colors' ) ),
			'default_value' => 'primary',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_LAYOUT + 20
		));

		$this->add_field( array
		(
			'key'           => 'size',
			'name'          => 'size',
			'title'         => __( 'Size' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'sm' => __( 'Small' ),
				'md' => __( 'Medium' ),
				'lg' => __( 'Large' ),
			),
			'default_value' => 'md',
			'order'         => PB_ORDER_TAB_LAYOUT + 30
		));

		$this->add_field( array
		(
			'key'           => 'outline',
			'name'          => 'outline',
			'title'         => __( 'Outline' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'order'         => PB_ORDER_TAB_LAYOUT + 40
		));

		$this->add_field( array
		(
			'key'           => 'block',
			'name'          => 'block',
			'title'         => __( 'Full Width' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'order'         => PB_ORDER_TAB_LAYOUT + 50
		));

		$this->add_field( array
		(
			'key'           => 'icon',
			'name'          => 'icon',
			'title'         => __( 'Icon' ),
			'description'   => '',
			'type'          => 'icon',
			'default_value' => '',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_LAYOUT + 60
		));

		$this->add_field( array
		(
			'key'           => 'icon_position',
			'name'          => 'icon_position',
			'title'         => __( 'Icon Position' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'left'  => __( 'Left' ),
				'right' => __( 'Right' )
			),
			'default_value' => 'left',
			'order'         => PB_ORDER_TAB_LAYOUT + 70
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		/**
		 * Attributes
		 * -----------------------------------------------------------
		 */

		$atts = array
		( 
			'class' => 'btn' 
		);

		// Link

		if ( $instance['link'] )
		{
			 $atts['href'] = esc_url( $instance['link'] );
		}

		// Link Tab

		if ( $instance['link_tab'] ) 
		{
			$atts['target'] = '_blank';
		}

		// Type

		if ( $instance['type'] )
		{
			if ( $instance['outline'] ) 
			{
				$atts['class'] .= " btn-outline-{$instance['type']}";
			}

			else
			{
				$atts['class'] .= " btn-{$instance['type']}";
			}
		}

		// Size

		if ( $instance['size'] )
		{
			$atts['class'] .= " btn-{$instance['size']}";
		}

		// Block

		if ( $instance['block'] )
		{
			$atts['class'] .= ' btn-block';
		}

		// Behavior

		if ( $instance['behavior'] == 'modal' )
		{
			$atts['data-toggle'] = 'modal';
		}

		elseif ( $instance['behavior'] == 'collapse' )
		{
			$atts['data-toggle'] = 'collapse';
		}

		// Icon

		if ( $instance['icon'] && $instance['icon_position'] ) 
		{
			$atts['class'] .= " pb-button-icon-{$instance['icon_position']}";
		}

		$atts = array_filter( $atts );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		?>

		<a<?php echo pb_render_attributes( $atts ); ?>>

			<?php $this->render_icon( $instance, 'left' ); ?>

			<?php echo esc_html( $instance['text'] ); ?>

			<?php $this->render_icon( $instance, 'right' ); ?>
		</a>
		
		<?php
		
		echo $args['after_widget'];
	}

	protected function render_icon( $instance, $position = 'left' )
	{
		if ( ! $instance['icon'] || $instance['icon_position'] != $position ) 
		{
			return;
		}

		echo pb()->icons->get_icon_html( $instance['icon'] );
	}
}

pb()->widgets->register( 'PB_Button_Widget' );
