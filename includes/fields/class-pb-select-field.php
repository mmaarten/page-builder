<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Select_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'select' );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'choices'  => array(),
			'multiple'  => false
		);

		$field = wp_parse_args( $field, $defaults );

		if ( ! is_array( $field['choices'] ) ) 
		{
			$field['choices'] = array();
		}

		if ( $field['multiple'] ) 
		{
			$field['default_value'] = (array) $field['default_value'];
		}

		elseif ( is_array( $field['default_value'] ) || is_object( $field['default_value'] ) )
		{
			$field['default_value'] = '';
		}

		return $field;
	}

	public function render( $field )
	{
		// Attributes

		$atts = array
		(
			'id'       => $field['id'],
			'name'     => $field['multiple'] ? $field['name'] . '[]' : $field['name'],
			'multiple' => $field['multiple'] ? 'multiple' : ''
		);

		$atts = array_filter( $atts );

		//
		
		printf( '<select%s>', pb_render_attributes( $atts ) );

		pb_dropdown_options( $field['choices'], $field['value'] );

		echo '</select>';
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

		if ( $field['multiple'] ) 
		{
			return $sanitized;
		}

		if ( $sanitized ) 
		{
			return reset( $sanitized );
		}

		return $field['default_value'];
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

pb()->field_types->register( 'PB_Select_Field' );

