<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Editor_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'editor' );

		add_action( 'admin_footer', array( $this, 'render_scripts' ) );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'tinymce' => array()
		);

		$field = wp_parse_args( $field, $defaults );

		return $field;
	}

	public function prepare( $field )
	{
		return $field;
	}

	public function render( $field )
	{
		$atts = array
		(
			'id'   => $field['id'],
			'name' => $field['name'],
		);

		$atts = array_filter( $atts );

		echo '<textarea' . pb_esc_attr( $atts ) . '>' . esc_textarea( $field['value'] ) . '</textarea>';
	}

	public function sanitize( $value, $field )
	{
		return $value;
	}

	public function render_scripts()
	{
		// TODO : I do not need to know about the editor

		if ( ! pb()->editor->is_screen() ) 
		{
			return;
		}

		// TODO : don't print editor. Set settings manually.
		// The editor is printed to fill the mce init array

		echo '<div class="hidden">';

		wp_editor( '', 'pb_content', array
		(
			'quicktags'     => false,
			'media_buttons' => false,
			'wpautop'       => true
		));

		echo '</div>';
	}
}

pb()->field_types->register_field( 'PB_Editor_Field' );
