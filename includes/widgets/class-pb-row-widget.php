<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Row_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'row', __( 'Row' ), array
		(
			'description' => __( 'Displays a row.' ),
			'controls'    => array( 'edit', 'copy', 'delete', 'toggle' ),
			'features'    => array
			( 
				'id', 
				'class',
				'margin_top', 
				'margin_bottom', 
				'padding_top', 
				'padding_bottom',
				'bg_image',
				'bg_image_size',
				'bg_position',
				'bg_type',
				'bg_color',
				'parallax',
				'bg_overlay'
			)
		));

		$vh_choices = array
		(
			'' => PB_THEME_DEFAULTS
		);

		for ( $i = 5; $i <= 100 ; $i += 5 ) 
		{ 
			$vh_choices[ $i ] = "$i%";
		}

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		$this->add_field( array
		(
			'key'           => 'layout',
			'name'          => 'layout',
			'title'         => __( 'Layout' ),
			'description'   => __( 'e.g. 1/3+2/3 creates 2 columns with 1/2 and 2/3 width.' ),
			'type'          => 'text',
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'container',
			'name'          => 'container',
			'title'         => __( 'Container' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'fixed' => __( 'Fixed Width' ),
				'fluid' => __( 'Full Width' )
			),
			'default_value' => 'fixed',
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		$this->add_field( array
		(
			'key'           => 'align_items',
			'name'          => 'align_items',
			'title'         => __( 'Aligns items' ),
			'description'   => __( 'Sets the vertical position of items inside this row.' ),
			'type'          => 'select',
			'choices'       => array
			(
				''       => PB_THEME_DEFAULTS,
				'start'  => __( 'Top' ),
				'center' => __( 'Center' ),
				'end'    => __( 'Bottom' )
			),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));

		$this->add_field( array
		(
			'key'           => 'gutters',
			'name'          => 'gutters',
			'title'         => __( 'Gutters' ),
			'description'   => __( 'Adds horizontal spacing between columns.' ),
			'type'          => 'true_false',
			'default_value' => 1,
			'order'         => PB_ORDER_TAB_GENERAL + 40
		));

		$this->add_field( array
		(
			'key'           => 'vh',
			'name'          => 'vh',
			'title'         => __( 'Height' ),
			'description'   => __( 'Height related to the viewport.' ),
			'type'          => 'select',
			'choices'       => $vh_choices,
			'default_value' => '',
			'order'         => PB_ORDER_TAB_GENERAL + 50
		));

		// Video

		$this->add_field( array
		(
			'key'           => 'video_source',
			'name'          => 'video_source',
			'title'         => __( 'Video Source' ),
			'description'   => __( 'Only mp4 format supported.' ),
			'type'          => 'url',
			'search'        => null,
			'default_value' => '',
			'order'         => PB_ORDER_TAB_BACKGROUND + 100
		));

		$this->add_field( array
		(
			'key'           => 'video_poster',
			'name'          => 'video_poster',
			'title'         => __( 'Video Poster' ),
			'description'   => __( 'Image to show while video is loading.' ),
			'type'          => 'image',
			'default_value' => '',
			'order'         => PB_ORDER_TAB_BACKGROUND + 110
		));

		add_action( 'admin_print_scripts', array( $this, 'render_admin_scripts' ) );
	}

	public function widget_html_attributes( $atts, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		if ( $instance['align_items'] ) 
		{
			$atts['class'] .= " d-flex align-items-{$instance['align_items']}";
		}

		if ( $instance['vh'] ) 
		{
			$atts['class'] .= " pb-vh-{$instance['vh']}";
		}

		if ( $instance['video_source'] )
		{
			$atts['class'] .= ' pb-video-bg';
		}

		return $atts;
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		/**
		 * Attributes
		 * -----------------------------------------------------------
		 */

		// Container

		$container = array
		(
			'class' => ''
		);

		if ( $instance['container'] == 'fluid' ) 
		{
			$container['class'] = 'container-fluid';
		}

		elseif ( $instance['container'] == 'fixed' ) 
		{
			$container['class'] = 'container';
		}

		$container = array_filter( $container );

		// Row

		$row = array
		(
			'class' => 'row'
		);

		if ( ! $instance['gutters'] ) 
		{
			$row['class'] .= ' no-gutters';
		}

		// Video

		$video = array
		(
			'playsinline' => 'playsinline',
			'autoplay'    => 'autoplay',
			'muted'       => 'muted',
			'loop'        => 'loop'
		);

		if ( $instance['video_poster'] ) 
		{
			list( $poster_url ) = wp_get_attachment_image_src( $instance['video_poster'], 'large' );

			if ( $poster_url ) 
			{
				$video['poster'] = esc_url( $poster_url );
			}
		}

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		?>
		
		<?php if ( count( $container ) ) : ?>
		<div<?php echo pb_render_attributes( $container ); ?>>
		<?php endif; ?>
			<div<?php echo pb_render_attributes( $row ); ?>>
				<?php pb()->widgets->the_child_widgets(); ?>
			</div>
		<?php if ( count( $container ) ) : ?>
		</div>
		<?php endif; ?>

		<?php if ( $instance['video_source'] ) : ?>

		<video<?php echo pb_render_attributes( $video ) ?>>
			<!--  
				Video needs to be muted, since Chrome 66+ will not autoplay video with sound.
				WCAG general accessibility recommendation is that media such as background video play 
				through only once. Loop turned on for the purposes of illustration; if removed, 
				the end of the video will fade in the same way created by pressing the "Pause" button  
			-->
			<source src="<?php echo esc_url( $instance['video_source'] ); ?>" type="video/mp4">
		</video>

		<?php endif;

		echo $args['after_widget'];
	}

	public function render_admin_scripts( $field )
	{
		// Checks if editor screen.
		
		if ( ! pb()->editor->is_screen() ) 
		{
			return;
		}

		$layouts = (array) apply_filters( 'pb_row_layouts', array
		(
			'12'      => '1/1',
			'6+6'     => '1/2+1/2',
			'4+8'     => '1/3+2/3',
			'8+4'     => '2/3+1/3',
			'3+9'     => '1/4+3/4',
			'9+3'     => '3/4+1/4',
			'4+4+4'   => '1/3+1/3+1/3',
			'3+6+3'   => '1/4+1/2+1/4',
			'3+3+3+3' => '1/4+1/4+1/4+1/4'
		));

		?>

		<script id="tmpl-pb-row-layout-picker" type="text/html">
			
			<div class="pb-layout-picker" data-target="#pb-input-layout">

				<?php

				foreach ( $layouts as $layout => $display )
				{
					$cols = explode( '+', $layout );

					printf( '<button type="button" class="pb-row" title="%s" data-layout="%s">', 
						esc_attr( $display ), esc_attr( $display ) );

					foreach ( $cols as $col ) 
					{
						printf( '<span class="pb-col-%d">', trim( $col ) );

						echo '<span class="inner"></span>';

						echo '</span>';
					}

					echo '</button>'; // .pb-row
				}

				?>

			</div>

		</script>

		<?php

	}
}

pb()->widgets->register( 'PB_Row_Widget' );
