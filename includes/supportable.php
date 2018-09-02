<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Supportable
{
	protected $features = array();

	public function __construct()
	{
		
	}

	public function get_features()
	{
		return $this->features;
	}

	public function add_support( $feature )
	{
		if ( func_num_args() > 1 ) 
		{
			$features = func_get_args();
		}

		else
		{
			$features = (array) $feature;
		}

		foreach ( $features as $feature ) 
		{
			$this->features[ $feature ] = $feature;
		}
	}

	public function remove_support( $feature )
	{
		unset( $this->features[ $feature ] );
	}

	public function supports( $feature )
	{
		return isset( $this->features[ $feature ] );
	}
}