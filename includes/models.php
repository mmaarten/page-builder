<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Models
{
	public function __construct()
	{

	}

	public function create_model( $args )
	{
		$defaults = array
		(
			'id'     => '',
			'type'   => '',
			'data'   => array(),
			'index'  => 0,
			'parent' => ''
		);

		$model = wp_parse_args( $args, $defaults );
		
		$model['data'] = wp_parse_args( $model['data'], $defaults['data'] );

		return $model;
	}

	public function get_post_models( $post_id = 0 )
	{
		$post = $this->get_post( $post_id );

		if ( ! $post ) 
		{
			return null;
		}

		$models = get_post_meta( $post->ID, '_pb_models', true );

		if ( ! is_array( $models ) ) 
		{
			$models = array();
		}

		return $models;
	}

	public function save_post_models( $models, $post_id = 0 )
	{
		$post = $this->get_post( $post_id );

		if ( ! $post ) 
		{
			return false;
		}

		$sanitized = array();

		foreach ( $models as $key => $model ) 
		{
			$model = $this->create_model( $model );
		
			$sanitized[ $key ] = $model;
		}

		update_post_meta( $post->ID, '_pb_models', $sanitized );

		return true;
	}

	protected function get_post( $post_id = 0 )
	{
		// Checks if post exists

		$post = get_post( $post_id );

		if ( ! $post ) 
		{
			return null;
		}

		// Checks of post type supports page builder

		if ( ! post_type_supports( $post->post_type, PB_POST_TYPE_FEATURE ) ) 
		{
			return null;
		}

		return $post;
	}
}

pb()->models = new PB_Models();
