<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Field
{
	public $id = null;

	public function __construct( $id )
	{
		$this->id = $id;

		add_filter( "pb/field/type={$this->id}"			, array( $this, 'field' ) );
		add_filter( "pb/prepare_field/type={$this->id}" , array( $this, 'prepare' ) );
		add_filter( "pb/sanitize_field/type={$this->id}", array( $this, 'sanitize' ), 10, 2 );

		add_action( "pb/render_field/type={$this->id}", array( $this, 'render' ) );

		add_action( 'pb_editor_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'pb_editor_render_scripts', array( $this, 'render_scripts' ) );
	}

	/**
	 * Field
	 *
	 * Called before field is registered.
	 *
	 * @param array $field The field
	 *
	 * @return array The field
	 */
	public function field( $field )
	{
		return $field;
	}

	/**
	 * Prepare
	 *
	 * Called before field input is rendered.
	 *
	 * @param array $field The field
	 *
	 * @return array The field
	 */
	public function prepare( $field )
	{
		return $field;
	}

	/**
	 * Render
	 *
	 * Renders field input element.
	 *
	 * @param array $field The field
	 */
	public function render( $field )
	{
		
	}

	/**
	 * Sanitize
	 *
	 * Sanitizes user input.
	 *
	 * @param mixed $value The input value
	 * @param array $field The field
	 *
	 * @return mixed
	 */
	public function sanitize( $value, $field )
	{
		return $value;
	}

	/**
	 * Translate
	 *
	 * Translates field value into a more readable format
	 *
	 * @param mixed $value The input value
	 * @param array $field The field
	 *
	 * @return mixed
	 */
	public function translate( $value, $field )
	{
		return $value;
	}

	public function enqueue_scripts()
	{
		
	}

	public function render_scripts()
	{
		
	}
}