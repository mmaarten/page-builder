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

	public function unregister_field( $field_id )
	{
		unset( $this->fields[ $field_id ] );
	}

	public function get_fields()
	{
		return $this->fields;
	}

	public function get_field( $field_id )
	{
		if ( isset( $this->fields[ $field_id ] ) ) 
		{
			return $this->fields[ $field_id ];
		}

		return null;
	}
}

pb()->field_types = new PB_Field_Types();
