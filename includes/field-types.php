<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Field_Types
{
	protected $field_types = array();

	public function __construct()
	{

	}

	public function register( $field_type )
	{
		if ( ! $field_type instanceof PB_Field ) 
		{
			$field_type = new $field_type();
		}

		$this->field_types[ $field_type->id ] = $field_type;
	}

	public function unregister( $id )
	{
		unset( $this->field_types[ $id ] );
	}

	public function get_field_types()
	{
		return $this->field_types;
	}

	public function get_field_type( $id )
	{
		if ( isset( $this->field_types[ $id ] ) ) 
		{
			return $this->field_types[ $id ];
		}

		return null;
	}
}

pb()->field_types = new PB_Field_Types();
