<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Image_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'image', __( 'Image' ), array
		(
			'description' => __( 'Displays an image.' ),
			'features'    => array( 'id', 'class', 'text_align', 'margin_top', 'margin_bottom' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'image',
			'name'          => 'image',
			'title'         => __( 'Image' ),
			'description'   => '',
			'type'          => 'image',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'size',
			'name'          => 'size',
			'title'         => __( 'Size' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => pb_get_image_size_choices(),
			'default_value' => 'large',
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$this->add_field( array
		(
			'key'           => 'link',
			'name'          => 'link',
			'title'         => __( 'Link' ),
			'description'   => '',
			'type'          => 'url',
			'default_value' => '',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));

		$this->add_field( array
		(
			'key'           => 'link_tab',
			'name'          => 'link_tab',
			'title'         => __( 'Open link in new window' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'order'         => PB_ORDER_TAB_GENERAL + 40
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */

		$this->add_field( array
		(
			'key'           => 'shape',
			'name'          => 'shape',
			'title'         => __( 'Shape' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				''          => PB_THEME_DEFAULTS,
				'thumbnail' => __( 'Thumbnail' ),
				'rounded'   => __( 'Rounded' ),
				'circle'    => __( 'Circle' )
			),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_LAYOUT + 10
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		/**
		 * Attributes
		 * -----------------------------------------------------------
		 */

		// Image

		$img = array
		(
			'class' => ''
		);

		if ( $instance['shape'] == 'thumbnail' ) 
		{
			$img['class'] .= ' img-thumbnail';
		}

		elseif ( $instance['shape'] == 'rounded' )
		{
			$img['class'] .= ' rounded';
		}

		elseif ( $instance['shape'] == 'circle' )
		{
			$img['class'] .= ' rounded-circle';
		}

		$img = array_filter( $img );

		// Link

		$link = array();

		if ( $instance['link'] ) 
		{
			$link['href']   = esc_url( $instance['link'] );
			$link['target'] = $instance['link_tab'] ? '_blank' : '_self';
		}

		$link = array_filter( $link );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];
		
		if ( count( $link ) ) 
		{
			printf( '<a%s>', pb_render_attributes( $link ) );
		}

		echo wp_get_attachment_image( $instance['image'], $instance['size'], false, $img );

		if ( count( $link ) ) 
		{
			echo '</a>';
		}
		
		echo $args['after_widget'];
	}

	public function preview( $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$this->preview_meta( $instance );

		?>

		<?php if ( $instance['image'] ) : ?>
		<div class="pb-widget-preview-content">
			<div class="pb-thumbnail">
				<?php echo wp_get_attachment_image( $instance['image'], 'thumbnail' ); ?>
			</div>
		</div>
		<?php endif; ?>

		<?php
	}
}

pb()->widgets->register( 'PB_Image_Widget' );
