<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

function pb()
{
	static $instance = null;

	if ( ! $instance ) 
	{
		$instance = new PB();
	}

	return $instance;
}

function pb_esc_attr( $atts )
{
	$str = '';

	foreach ( $atts as $name => $value ) 
	{
		$str .= sprintf( ' %s="%s"', $name, esc_attr( $value ) );
	}

	return $str;
}

function pb_sort_order( $a, $b )
{
    if ( $a['order'] == $b['order'] ) 
    {
        return 0;
    }

    return $a['order'] < $b['order'] ? -1 : 1;
}

function pb_dropdown_options( $options, $selected = '' )
{
	foreach ( $options as $value => $text ) 
	{
		printf( '<option value="%s"%s>%s</option>', 
			esc_attr( $value ), selected( $value, $selected, false ), esc_html( $text ) );
	}
}

function pb_stripslashes( $value )
{
	if ( is_array( $value ) ) 
	{
		return array_map( 'pb_stripslashes', $value );
	}

	return stripslashes( $value );
}

function pb_get_grid_breakpoints()
{
	return apply_filters( 'pb/grid_breakpoints', array
	(
		// slug => css class format
		'xs' => '%s-%d', 
		'sm' => '%s-sm-%d', 
		'md' => '%s-md-%d', 
		'lg' => '%s-lg-%d', 
		'xl' => '%s-xl-%d', 
	));
}
