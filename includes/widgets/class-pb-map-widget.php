<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Map_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'map', __( 'Map' ), array
		(
			'description' => __( 'Displays a Google map.' ),
			'features'    => array( 'id', 'class', 'margin_top', 'margin_right' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		// Markers

		$this->add_field( array
		(
			'key'          => 'markers',
			'name'         => 'markers',
			'title'        => __( 'Markers' ),
			'description'  => '',
			'type'         => 'repeater',
			'add_row_text' => __( 'Add Marker' ),
			'order'        => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'marker_image',
			'name'          => 'image',
			'title'         => __( 'Image' ),
			'description'   => __( 'Leave empty to use default.' ),
			'type'          => 'image',
			'default_value' => '',
			'order'         => 0,
			'parent'        => 'markers'
		));

		$this->add_field( array
		(
			'key'           => 'marker_address',
			'name'          => 'address',
			'title'         => __( 'Address' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => '',
			'order'         => 0,
			'parent'        => 'markers'
		));

		$this->add_field( array
		(
			'key'           => 'marker_info',
			'name'          => 'info',
			'title'         => __( 'Info' ),
			'description'   => '',
			'type'          => 'textarea',
			'rows'          => 2,
			'default_value' => '',
			'order'         => 10,
			'parent'        => 'markers'
		));

		// Zoom

		$this->add_field( array
		(
			'key'           => 'zoom',
			'name'          => 'zoom',
			'title'         => __( 'Zoom' ),
			'description'   => __( 'Only applied when 1 marker.' ),
			'type'          => 'number',
			'default_value' => 8,
			'order'         => PB_ORDER_TAB_GENERAL + 20
		));

		// Ratio

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
			'default_value' => '4by3',
			'order'         => PB_ORDER_TAB_GENERAL + 30
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		/**
	     * Markers
	     * ---------------------------------------------------------------
		 */

		$markers = array();

		foreach ( $instance['markers'] as $marker ) 
		{
			// Address

			$geocode = PB_Util::get_geocode( array
			(
				'address' => $marker['address']
			));

			if ( is_wp_error( $geocode ) ) 
			{
				trigger_error( sprintf( __( 'Map location "%s" could not be found: %s' ), $marker['address'], $geocode->get_error_message() ) );

				continue;
			}

			$location = $geocode->results[0]->geometry->location;

			// Image

			$image = '';

			if ( $marker['image'] ) 
			{
				$image = wp_get_attachment_image_src( $marker['image'], 'thumbnail' );

				if ( $image ) 
				{
					$image = $image[0];
				}
			}

			$markers[] = array
			(
				'lat'   => $location->lat,
				'lng'   => $location->lng,
				'image' => $image,
				'info'  => $marker['info']
			);
		}

		/**
		 * Attributes
		 * -----------------------------------------------------------
		 */

		static $counter = 0;

		$atts = array
		(
			'id'    => sprintf( 'pb-map-%s', ++$counter ),
			'class' => ''
		);
	
		if ( $instance['ratio'] ) 
		{
			$atts['class'] = "embed-responsive embed-responsive-{$instance['ratio']}";
		}

		// Removes empty attributes

		$atts = array_filter( $atts );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		printf( '<div%s></div>', pb_render_attributes( $atts ) );

		// Javascript

		$map_options = array
		(
			'markers' => $markers,
			'zoom'    => $instance['zoom']
		);

		?>

		<script type="text/javascript">
			
			jQuery( document ).ready( function()
			{
				jQuery( '#<?php echo esc_js( $atts['id'] ) ?>' ).pbMap( <?php echo json_encode( $map_options ); ?> );
			});

		</script>

		<?php

		echo $args['after_widget'];
	}

	public function preview( $instance )
	{
		$this->preview_meta( $instance );

		if ( ! is_array( $instance['markers'] ) || ! count( $instance['markers'] ) ) 
		{
			echo '<div class="pb-widget-preview-content">';

			pb_notice( __( 'No markers set.' ), 'info' );

			echo '</div>';

			return;
		}

		echo '<div class="pb-widget-preview-content">';

		printf( '<p><strong>%s</strong></p>', __( 'Found locations:' ) );

		echo '<ul>';

		foreach ( $instance['markers'] as $marker ) 
		{
			$geocode = PB_Util::get_geocode( array
			(
				'address' => $marker['address']
			));

			if ( is_wp_error( $geocode ) )
			{
				$text = sprintf( __( '(Address "%s" not found: %s)' ), $marker['address'], $geocode->get_error_message() );
			}

			else
			{
				$text = $geocode->results[0]->formatted_address;
			}

			printf( '<li class="dashicons-before dashicons-location">%s</li>', esc_html( $text ) );
		}

		echo '</ul>';
	}

	public function enqueue_scripts()
	{
		$google_maps_url = add_query_arg( array
		(
			'key' => apply_filters( 'pb_google_api_key', '' )
		), '//maps.googleapis.com/maps/api/js' );

		wp_enqueue_script( 'google-maps-api', $google_maps_url, null, '3', true );
	}
}

pb()->widgets->register( 'PB_Map_Widget' );
