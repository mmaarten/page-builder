<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Gallery_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'gallery', __( 'Gallery' ), array
		(
			'description' => __( 'Displays a gallery.' ),
			'features'    => array( 'id', 'class', 'margin_top', 'margin_bottom' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		$this->add_field( array
		(
			'key'           => 'images',
			'name'          => 'images',
			'title'         => __( 'Images' ),
			'description'   => '',
			'type'          => 'image',
			'multiple'      => true,
			'default_value' => array(),
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'image_size',
			'name'          => 'image_size',
			'title'         => __( 'Image Size' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => pb_get_image_size_choices(),
			'default_value' => 'large',
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$this->add_field( array
		(
			'key'           => 'image_ratio',
			'name'          => 'image_ratio',
			'title'         => __( 'Image Ratio' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				''      => __( '- None -' ),
			), pb()->options->get( 'image_ratios' ) ),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));

		$this->add_field( array
		(
			'key'           => 'link',
			'name'          => 'link',
			'title'         => __( 'Link Image To' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				''         => __( '- None -' ),
				'image'    => __( 'Image' ),
				'page'     => __( 'Page' ),
				'lightbox' => __( 'Lightbox' )
			),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 40
		));

		// Column Width

		$this->add_field( array
		(
			'key'           => 'column_width',
			'name'          => 'cols',
			'title'         => __( 'Column Width' ),
			'description'   => '',
			'type'          => 'group',
			'layout'        => 'row',
			'order'         => PB_ORDER_TAB_GENERAL + 50
		));

		$breakpoints = pb()->options->get( 'grid_breakpoint_formats' );

		$order = 0;

		foreach ( $breakpoints as $breakpoint => $format ) 
		{
			$choices = pb_get_column_choices( $breakpoint );

			$this->add_field( array
			(
				'key'           => "tab_{$breakpoint}",
				'name'          => "tab_{$breakpoint}",
				'title'         => strtoupper( $breakpoint ),
				'description'   => '',
				'type'          => 'tab',
				'order'         => $order,
				'parent'        => 'column_width'
			));

			$this->add_field( array
			(
				'key'           => "col_{$breakpoint}",
				'name'          => "$breakpoint",
				'title'         => __( '' ),
				'description'   => '',
				'type'          => 'select',
				'choices'       => $choices,
				'default_value' => $breakpoint == 'xs' ? 3 : '',
				'order'         => $order + 10,
				'parent'        => 'column_width'
			));

			$order += 20;
		}
	}

	public function widget( $args, $instance )
	{
		static $counter = 0;

		$counter++;

		$instance = wp_parse_args( $instance, $this->get_defaults() );

		/**
		 * Attributes
		 * -----------------------------------------------------------
		 */

		// Creates id for use with Fancybox

		if ( preg_match( '/id="(.*?)"/', $args['before_widget'], $matches ) )
		{
			list(, $widget_id ) = $matches;

			$gallery_id = "{$widget_id}-gallery";
		}

		else
		{
			$gallery_id = "gallery-{$counter}";
		}

		// Column

		$column = array
		(
			'class' => ''
		);

		$breakpoints = pb()->options->get( 'grid_breakpoint_formats' );

		foreach ( $breakpoints as $breakpoint => $format ) 
		{
			if ( isset( $instance['cols'][ $breakpoint ] ) && $value = $instance['cols'][ $breakpoint ] ) 
			{
				$column['class'] .= ' ' . sprintf( $format, 'col', $value );
			}
		}

		$column = array_filter( $column );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		?>

		<div id="<?php echo esc_attr( $gallery_id ); ?>" class="pb-gallery">

			<div class="row no-gutters">

				<?php foreach ( $instance['images'] as $attachment_id ): 

					// Wrapper

					$wrapper = array();

					$elem = 'div';

					if ( $instance['link'] ) 
					{
						$elem = 'a';

						if ( $instance['link'] == 'image' ) 
						{
							$wrapper['href'] = wp_get_attachment_url( $attachment_id );
						}

						elseif ( $instance['link'] == 'page' ) 
						{
							$wrapper['href'] = get_attachment_link( $attachment_id );
						}

						elseif ( $instance['link'] == 'lightbox' ) 
						{
							list( $image_url, $image_width, $image_height ) = wp_get_attachment_image_src( $attachment_id, 'full' );

							$wrapper['href'] = $image_url;

							// Fancybox
							$wrapper['data-fancybox'] = $gallery_id;
							$wrapper['data-width']    = $image_width;
							$wrapper['data-height']   = $image_height;
						}
					}

					if ( $instance['image_ratio'] ) 
					{
						$wrapper['class'] = "d-block pb-cover-image pb-cover-image-{$instance['image_ratio']}";
					}

					$wrapper = array_filter( $wrapper );

				?>
				<div<?php echo pb_render_attributes( $column ); ?>>

					<div class="pb-gallery-item">

						<?php if ( count( $wrapper ) ) : ?>
						<<?php echo $elem . pb_render_attributes( $wrapper ); ?>>
						<?php endif; ?>

						<?php echo wp_get_attachment_image( $attachment_id, $instance['image_size'] ); ?>

						<?php if ( count( $wrapper ) ) : ?>
						</<?php echo $elem; ?>>
						<?php endif; ?>

					</div>

				</div>
				<?php endforeach; ?>

			</div>

		</div>

		<?php
		
		echo $args['after_widget'];
	}

	public function preview( $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		$this->preview_meta( $instance );
		
		// 

		if ( ! count( $instance['images'] ) ) 
		{
			pb_notice( __( 'No images set.' ) );

			return;
		}

		echo '<div class="pb-widget-preview-content">';

		echo '<ul class="pb-thumbnails">';

		foreach ( $instance['images'] as $attachment_id ) 
		{
			printf( '<li class="pb-thumbnail"><img src="%s"></li>', wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) );
		}

		echo '</ul>';

		echo '</div>';
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style( 'jquery-fancybox' );
		wp_enqueue_script( 'jquery-fancybox' );
	}
}

pb()->widgets->register( 'PB_Gallery_Widget' );
