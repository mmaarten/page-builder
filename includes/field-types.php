<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Field_Types
{
	protected $fields = array();

	public function __construct()
	{
		
	}

	public function register_field( $field )
	{
		if ( ! $field instanceof PB_Field ) 
		{
			$field = new $field();
		}

		$this->fields[ $field->id ] = $field;
	}

	public function unregister_field( $id )
	{
		unset( $this->fields[ $id ] );
	}

	public function get_fields()
	{
		return $this->fields;
	}

	public function get_field( $id )
	{
		if ( isset( $this->fields[ $id ] ) ) 
		{
			return $this->fields[ $id ];
		}

		return null;
	}
}

pb()->field_types = new PB_Field_Types();
