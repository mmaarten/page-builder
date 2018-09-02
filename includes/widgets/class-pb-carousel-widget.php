<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Carousel_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'carousel', __( 'Carousel' ), array
		(
			'description' => __( 'Displays a carousel.' ),
			'features'    => array( 'id', 'class', 'margin_top', 'margin_bottom' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		// Images

		$this->add_field( array
		(
			'key'         => 'images',
			'name'        => 'images',
			'title'       => __( 'Images' ),
			'description' => '',
			'type'        => 'repeater',
			'order'       => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'         => 'images_id',
			'name'        => 'id',
			'title'       => __( 'Image' ),
			'description' => '',
			'type'        => 'image',
			'multiple'    => false,
			'order'       => 10,
			'parent'      => 'images'
		));

		$this->add_field( array
		(
			'key'           => 'images_link',
			'name'          => 'link',
			'title'         => __( 'Link' ),
			'description'   => '',
			'type'          => 'url',
			'default_value' => '',
			'order'         => 20,
			'parent'        => 'images'
		));

		$this->add_field( array
		(
			'key'           => 'images_link_tab',
			'name'          => 'link_tab',
			'title'         => __( 'Open link in new window' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'order'         => 30,
			'parent'        => 'images'
		));

		// Size

		$this->add_field( array
		(
			'key'           => 'size',
			'name'          => 'image_size',
			'title'         => __( 'Image Size' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => pb_get_image_size_choices(),
			'default_value' => 'large',
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		// Items

		$this->add_field( array
		(
			'key'                   => 'items',
			'name'                  => 'items',
			'title'                 => __( 'Items' ),
			'description'           => __( 'Select the amount of images to display per screen size.' ),
			'description_placement' => 'label',
			'type'                  => 'group',
			'layout'                => 'row',
			'order'                 => PB_ORDER_TAB_GENERAL + 30
		));

		$breakpoints = pb()->options->get( 'grid_breakpoint_formats' );

		$order = 0;

		foreach ( $breakpoints as $breakpoint => $format ) 
		{
			$this->add_field( array
			(
				'key'           => "items_{$breakpoint}_tab",
				'name'          => "items_{$breakpoint}_tab",
				'title'         => strtoupper( $breakpoint ),
				'type'          => 'tab',
				'parent'        => 'items',
				'order'         => $order
			));

			$this->add_field( array
			(
				'key'           => "items_{$breakpoint}",
				'name'          => $breakpoint,
				'title'         => __( '' ),
				'description'   => __( '' ),
				'type'          => 'number',
				'default_value' => $breakpoint == 'xs' ? 1 : '',
				'parent'        => 'items',
				'order'         => $order + 10
			));

			$order += 20;
		}

		// Features

		$this->add_field( array
		(
			'key'           => 'features',
			'name'          => 'features',
			'title'         => __( 'Features' ),
			'description'   => '',
			'type'          => 'checkboxes',
			'choices'       => array
			(
				'loop'     => __( 'Loop' ),
				'autoplay' => __( 'Autoplay' ),
				'nav'      => __( 'Display controls' ),
				'dots'     => __( 'Display indicators' ),
			),
			'default_value' => array( 'loop', 'autoplay', 'dots' ),
			'preview'       => false,
			'order'         => PB_ORDER_TAB_GENERAL + 40
		));
	}

	public function widget( $args, $instance )
	{
		static $counter = 0;

		$instance = wp_parse_args( $instance, $this->get_defaults() );

		if ( empty( $instance['images'] ) ) 
		{
			return;
		}

		// Generates carousel id

		$carousel_id = sprintf( 'owl-carousel-%s', ++$counter );

		// Output

		echo $args['before_widget'];

		?>

		<div id="<?php echo esc_attr( $carousel_id ); ?>" class="owl-carousel owl-theme">
			<?php 

			foreach ( $instance['images'] as $image ) 
			{
				// Link

				$link = array();

				if ( $image['link'] ) 
				{
					$link['href']   = $image['link'];
					$link['target'] = $image['link_tab'] ? '_blank' : '_self';
				}

				$link = array_filter( $link );

				// Output

				if ( count( $link ) ) 
				{
					printf( '<a%s>', pb_render_attributes( $link ) );
				}

				echo wp_get_attachment_image( $image['id'], $instance['image_size'] );

				if ( count( $link ) ) 
				{
					echo '</a>';
				}
			}

			?>
		</div><!-- .owl-carousel -->

		<?php

		echo $args['after_widget'];

		/**
		 * JavaScript
		 * -----------------------------------------------------------
		 */

		// Sets the amount of items to display per screen size.

		$responsive = array();

		$breakpoints = pb()->options->get( 'grid_breakpoints' );

		foreach ( $breakpoints as $breakpoint => $width ) 
		{
			if ( ! isset( $instance['items'][ $breakpoint ] ) ) 
			{
				continue;
			}

			$items = intval( $instance['items'][ $breakpoint ] );

			if ( ! $items ) 
			{
				continue;
			}

			$responsive[ $width ] = array
			(
				'items' => $items
			);
		}

		//

		$options = array
		(
			'responsive' => $responsive,
			'loop'       => in_array( 'loop'    , $instance['features'] ) ? 1 : 0,
			'autoplay'   => in_array( 'autoplay', $instance['features'] ) ? 1 : 0,
			'nav'        => in_array( 'nav'     , $instance['features'] ) ? 1 : 0,
			'dots'       => in_array( 'dots'    , $instance['features'] ) ? 1 : 0
		);

		?>

		<script type="text/javascript">

			jQuery( document ).ready( function()
			{
		  		jQuery( '#<?php echo esc_attr( $carousel_id ); ?>' ).owlCarousel( <?php echo json_encode( $options ); ?> );
			});

		</script>

		<?php
	}

	public function preview( $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$this->preview_meta( $instance );
		
		// 

		if ( ! $instance['images'] ) 
		{
			pb_notice( __( 'No images set.' ) );

			return;
		}

		echo '<div class="pb-widget-preview-content">';

		echo '<ul class="pb-thumbnails">';

		foreach ( $instance['images'] as $image ) 
		{
			printf( '<li class="pb-thumbnail"><img src="%s"></li>', wp_get_attachment_image_url( $image['id'], 'thumbnail' ) );
		}

		echo '</ul>';

		echo '</div>';
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style( 'owl-carousel' );
		wp_enqueue_style( 'owl-carousel-theme' );
		wp_enqueue_script( 'owl-carousel' );
	}
}

pb()->widgets->register( 'PB_Carousel_Widget' );
