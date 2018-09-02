<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Checkboxes_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'checkboxes' );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'choices' => array()
		);

		$field = wp_parse_args( $field, $defaults );

		if ( ! is_array( $field['choices'] ) ) 
		{
			$field['choices'] = array();
		}

		if ( ! is_array( $field['default_value'] ) )
		{
			$field['default_value'] = (array) $field['default_value'];
		}

		return $field;
	}

	public function render( $field )
	{
		if ( ! $field['choices'] ) 
		{
			return;
		}

		$wrapper = array
		(
			'id'    => $field['id'],
			'class' => 'pb-checkboxes'
		);

		$wrapper = array_filter( $wrapper );

		printf( '<ul%s>', pb_render_attributes( $wrapper ) );

		foreach ( $field['choices'] as $option_value => $option_text ) 
		{
			$atts = array
			(
				'type'    => 'checkbox',
				'name'    => $field['name'] . '[]',
				'value'   => $option_value,
				'checked' => in_array( $option_value, $field['value'] )
			);

			$atts = array_filter( $atts );

			printf( '<li><label><input%s> %s</label></li>', pb_render_attributes( $atts ), esc_html( $option_text ) );
		}

		echo '</ul>';
	}

	public function sanitize( $value, $field )
	{
		$value = (array) $value;

		$sanitized = array();

		foreach( $value as $key )
		{
			if ( ! isset( $field['choices'][ $key ] ) ) 
			{
				continue;
			}

			$sanitized[ $key ] = $key;
		}

		return $sanitized;
	}

	public function translate( $value, $field )
	{
		$value = (array) $value;

		$translated = array();

		foreach( $value as $key )
		{
			if ( ! isset( $field['choices'][ $key ] ) ) 
			{
				continue;
			}

			$translated[ $key ] = esc_html( $field['choices'][ $key ] );
		}

		return implode( ', ', $translated );
	}
}

pb()->field_types->register( 'PB_Checkboxes_Field' );
