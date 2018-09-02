<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Youtube_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'youtube', __( 'YouTube' ), array
		(
			'description' => __( 'Displays a YouTube video.' ),
			'features'    => array
			(
				'id', 
				'class',
				'margin_top',
				'margin_bottom'
			)
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'code',
			'name'          => 'code',
			'title'         => __( 'Embed code' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'preview'       => true,
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'features',
			'name'          => 'features',
			'title'         => __( 'Features' ),
			'description'   => '',
			'type'          => 'checkboxes',
			'choices'       => array
			(
				'rel'      => __( 'Show related videos (after the video ends).' ),
				'controls' => __( 'Show playback controls.' ),
				'info'     => __( 'Show title and playback options.' ),
				'autoplay' => __( 'Automatically play after video is loaded.' ),
			),
			'default_value' => array( 'controls' ),
			'preview'       => false,
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */

		$this->add_field( array
		(
			'key'           => 'ratio',
			'name'          => 'ratio',
			'title'         => __( 'Ratio' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				''      => __( '- None -' ),
			), pb()->options->get( 'embed_ratios' ) ),
			'default_value' => '16by9',
			'preview'       => false,
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

		$atts = array
		(
			'class' => ''
		);

		if ( $instance['ratio'] ) 
		{
			$atts['class'] .= "embed-responsive embed-responsive-{$instance['ratio']}";
		}

		// Removes empty attributes

		$atts = array_filter( $atts );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		// Opening Tag
		
		if ( count( $atts ) ) 
		{
			printf( '<div%s>', pb_render_attributes( $atts ) );
		}

		// Inner

		$url = sprintf( 'https://www.youtube.com/embed/%s', urlencode( $instance['code'] ) );

		$url = add_query_arg( array
		(
			'rel'      => in_array( 'rel'     , (array) $instance['features'] ) ? 1 : 0,
			'controls' => in_array( 'controls', (array) $instance['features'] ) ? 1 : 0,
			'showinfo' => in_array( 'info'    , (array) $instance['features'] ) ? 1 : 0,
			'autoplay' => in_array( 'autoplay', (array) $instance['features'] ) ? 1 : 0,
		), $url );

		printf( '<iframe src="%s" class="embed-responsive-item" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
			$url );
		
		// Closing Tag

		if ( count( $atts ) ) 
		{
			echo '</div>';
		}

		//
		
		echo $args['after_widget'];
	}

	public function preview( $instance )
	{
		$this->preview_meta( $instance );

		if ( ! isset( $instance['code'] ) || ! $instance['code'] )
		{
			return;
		}
		
		$data = PB_Util::get_youtube_video_data( $instance['code'] );

		if ( is_wp_error( $data ) ) 
		{
			echo '<div class="pb-widget-preview-content">';

			pb_notice( sprintf( __( 'Unable to load preview: %s' ), $data->get_error_message() ), 'error' );

			echo '</div>';

			return;
		}

		$snippet = $data->items[0]->snippet;
		$thumbnail = $snippet->thumbnails->medium;

		?>

		<div class="pb-media-box">

			<div class="pb-media-box-left">
				<?php 

					printf( '<img src="%s" class="pb-media-box-object pb-thumbnail" width="%s" height="%s">', 
						esc_url( $thumbnail->url ), esc_attr( $thumbnail->width ), esc_attr( $thumbnail->height ) );

				 ?>
			</div><!-- .pb-media-box-left -->

			<div class="pb-media-box-body">
				<h3 class="pb-media-box-heading"><?php 

					 $snippet->title ? esc_html_e( $snippet->title ) : esc_html_e( '(Untitled)' )

				 ?></h3>
				 <?php if ( $snippet->description ): ?>
				 <p><?php echo wp_trim_words( $snippet->description, 10 ); ?></p>
				 <?php endif; ?>
			</div><!-- .pb-media-box-body -->

		</div><!-- .pb-media-box -->

		<?php
	}
}

pb()->widgets->register( 'PB_Youtube_Widget' );

