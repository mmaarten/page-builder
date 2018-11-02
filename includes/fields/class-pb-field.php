<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Field
{
	public $id = null;

	public function __construct( $id )
	{
		$this->id = $id;

		add_filter( "pb/field/type={$this->id}"          , array( $this, 'field' ) );
		add_filter( "pb/prepare_field/type={$this->id}"  , array( $this, 'prepare' ) );
		add_filter( "pb/render_field/type={$this->id}"   , array( $this, 'render' ) );
		add_filter( "pb/sanitize_field/type={$this->id}" , array( $this, 'sanitize' ), 10, 2 );
		add_filter( "pb/translate_field/type={$this->id}", array( $this, 'translate' ), 10, 2 );
	}

	public function field( $field )
	{
		return $field;
	}

	public function prepare( $field )
	{
		return $field;
	}

	public function render( $field )
	{
		
	}

	public function sanitize( $value, $field )
	{
		return $value;
	}

	public function translate( $value, $field )
	{
		return esc_html( $value );
	}
}
