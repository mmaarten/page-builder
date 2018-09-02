<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Options
{
	protected $data = array();

	public function __construct()
	{
		
	}

	public function get( $key, $group = 'common' )
	{
		if ( isset( $this->data[ $group ][ $key ] ) ) 
		{
			return $this->data[ $group ][ $key ];
		}

		return null;
	}

	public function set( $key, $value, $group = 'common' )
	{
		$value = apply_filters( 'pb_option', $value, $key, $group );
		$value = apply_filters( "pb_option/group={$group}", $value, $key, $group );

		$this->data[ $group ][ $key ] = $value;
	}
}

pb()->options = new PB_Options();
