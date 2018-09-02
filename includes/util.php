<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Util
{
	const GEOCODE_TRANSIENT = 'pb_geocode';
	const YOUTUBE_TRANSIENT = 'pb_youtube';

	/**
	 * Get Geocode
	 *
	 * @link https://developers.google.com/maps/documentation/geocoding/start
	 * @param array $args A list of parameters to include in the request.
	 * @param boolean $from_cache Whether return cached data if found.
	 * @return stdClass|WP_Error The result on success or WP_Error on failure.
	 */
	static public function get_geocode( $args, $from_cache = true )
	{
		$defaults = array
		(
			'key' => apply_filters( 'pb_google_api_key', '' )
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Checks cache
		 * -----------------------------------------------------------
		 */

		// Creates cache key
		
		$cache_key = md5( serialize( $args ) );

		// Gets catched data

		$transient = get_transient( self::GEOCODE_TRANSIENT );

		if ( ! is_array( $transient ) ) 
		{
			$transient = array();
		}

		// Checks if cached

		if ( $from_cache && isset( $transient[ $cache_key ] ) ) 
		{
			return $transient[ $cache_key ];
		}

		/**
		 * Gets data
		 * -----------------------------------------------------------
		 */

		$response = wp_remote_get( 'https://maps.googleapis.com/maps/api/geocode/json', array
		(
			'body' => $args
		));

		if ( is_wp_error( $response ) ) 
		{
			return $response;
		}

		$result = json_decode( $response['body'] );

		if ( json_last_error() != JSON_ERROR_NONE ) 
		{
			return new WP_Error( 'json_decode', __( 'Invalid response' ) );
		};

		if ( $result->status != 'OK' ) 
		{
			$return = new WP_Error( 'status', __( 'Unable to get geocode' ) );
		}

		else
		{
			$return = $result;
		}

		/**
		 * Saves data to cache
		 * -----------------------------------------------------------
		 */

		$transient[ $cache_key ] = $return;

		set_transient( self::GEOCODE_TRANSIENT, $transient );

		/* -------------------------------------------------------- */

		return $return;
	}

	static public function get_youtube_video_data( $video_id, $from_cache = true )
	{
		/**
		 * Checks cache
		 * -----------------------------------------------------------
		 */

		$transient = get_transient( self::YOUTUBE_TRANSIENT );

		if ( ! is_array( $transient ) ) 
		{
			$transient = array();
		}

		if ( $from_cache && isset( $transient[ $video_id ] ) ) 
		{
			return $transient[ $video_id ];
		}

		/**
		 * Gets data
		 * -----------------------------------------------------------
		 */

		$url = add_query_arg( array
		(
			'id'   => $video_id,
			'part' => 'snippet',
			'key'  => apply_filters( 'pb_google_api_key', '' ) 
		), 'https://www.googleapis.com/youtube/v3/videos' );

		$contents = @file_get_contents( $url );

		if ( $contents === false ) 
		{
			return new WP_Error( 'file_get_contents', __( 'Unable to get data' ) );
		}

		$data = json_decode( $contents );

		if ( json_last_error() != JSON_ERROR_NONE ) 
		{
			return new WP_Error( 'json_decode', __( 'Invalid response' ) );
		};

		if ( empty( $data->items ) ) 
		{
			$return = new WP_Error( 'not_found', __( 'Video not found' ) );
		}

		else
		{
			$return = $data;
		}

		/**
		 * Saves data to cache
		 * -----------------------------------------------------------
		 */

		$transient[ $video_id ] = $return;
		
		set_transient( self::YOUTUBE_TRANSIENT, $transient );

		/* -------------------------------------------------------- */

		return $return;
	}
}