<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Models
{
	public function __construct()
	{
		
	}

	public function create_model( $args )
	{
		// Defaults
		$defaults = array
		(
			'id'     => '',
			'type'   => '',
			'data'   => array(),
			'index'  => 0,
			'parent' => '',
		);

		// Create
		$model         = wp_parse_args( $args, $defaults );
		$model['data'] = wp_parse_args( $model['data'], $defaults['data'] );

		// Filter
		$model = apply_filters( 'pb/model'                      , $model );
		$model = apply_filters( "pb/model/type={$model['type']}", $model );

		// Return
		return $model;
	}

	public function get_models( $post_id, $search = array() )
	{
		// Check if models
		if ( ! metadata_exists( 'post', $post_id, 'pb_models' ) ) 
		{
			return null;
		}

		// Get models
		$models = get_post_meta( $post_id, 'pb_models', true );

		// Search
		$models = wp_filter_object_list( $models, $search );

		// Return
		return $models;
	}

	public function save_models( $models, $post_id )
	{
		// Sanitize

		$sanitized = array();

		foreach ( $models as $model ) 
		{
			$model = $this->create_model( $model );

			$sanitized[ $model['id'] ] = $model;
		}

		$models = $sanitized;

		// Save

		return update_post_meta( $post_id, 'pb_models', $models ) !== false;
	}
}

pb()->models = new PB_Models();
