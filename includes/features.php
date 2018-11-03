<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Features
{
	protected $features = array();

	public function __construct()
	{
		
	}

	public function register_feature( $feature )
	{
		if ( ! $feature instanceof PB_Feature ) 
		{
			$feature = new $feature();
		}

		$this->features[ $feature->id ] = $feature;
	}

	public function unregister_feature( $id )
	{
		unset( $this->features[ $id ] );
	}

	public function get_features()
	{
		return $this->features;
	}

	public function get_feature( $id )
	{
		if ( isset( $this->features[ $id ] ) ) 
		{
			return $this->features[ $id ];
		}

		return null;
	}
}

pb()->features = new PB_Features();
