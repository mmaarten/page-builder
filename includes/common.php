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

function pb_button( $args )
{
	$defaults = array
	(
		'text'     => __( 'Button' ),
		'link'     => '',
		'link_tab' => false,
		'type'     => 'primary',
		'size'     => '',
		'outline'  => false,
		'block'    => false,
		'behavior' => ''
	);

	$args = wp_parse_args( $args, $defaults );

	/**
	 * Attributes
	 * -----------------------------------------------------------
	 */

	$atts = array
	( 
		'class' => 'btn' 
	);

	// Link

	if ( $args['link'] )
	{
		 $atts['href'] = esc_url( $args['link'] );
	}

	// Link Tab

	if ( $args['link_tab'] ) 
	{
		$atts['target'] = '_blank';
	}

	// Type

	if ( $args['type'] )
	{
		if ( $args['outline'] ) 
		{
			$atts['class'] .= " btn-outline-{$args['type']}";
		}

		else
		{
			$atts['class'] .= " btn-{$args['type']}";
		}
	}

	// Size

	if ( $args['size'] )
	{
		$atts['class'] .= " btn-{$args['size']}";
	}

	// Block

	if ( $args['block'] )
	{
		$atts['class'] .= ' btn-block';
	}

	// Behavior

	if ( $args['behavior'] == 'modal' )
	{
		$atts['data-toggle'] = 'modal';
	}

	elseif ( $args['behavior'] == 'collapse' )
	{
		$atts['data-toggle'] = 'collapse';
	}

	printf( '<a%s>%s</a>', pb_render_attributes( $atts ), esc_html( $args['text'] ) );
}

function pb_input_field( $atts = array() )
{
    $atts = wp_parse_args( $atts );

    // removes empty attributes
    $atts = array_filter( $atts );

    printf( '<input%s>', pb_render_attributes( $atts ) );
}

function pb_hidden_field( $atts = array() )
{
    $atts = wp_parse_args( $atts );

    $atts['type'] = 'hidden';

    pb_input_field( $atts );
}

function pb_text_field( $atts = array() )
{
    $atts = wp_parse_args( $atts );

    $atts['type'] = 'text';

    pb_input_field( $atts );
}

function pb_post_pagination( $query )
{
    if ( $query->is_singular() )
    {
        return;
    }

    $total = intval( $query->max_num_pages );

    // Stops if there's only 1 page.

    if ( $total <= 1 )
    {
        return;
    }

    $paged     = $query->get( 'paged' ) ? absint( $query->get( 'paged' ) ) : 1;
    $prev_page = $paged > 1 ? $paged - 1 : false;
    $next_page = $paged < $total ? $paged + 1 : false;
    $total     = intval( $query->max_num_pages );

    printf( '<nav class="pb-pagination" aria-label="%s">', esc_attr__( 'Page navigation' ) );

    echo '<ul class="pagination">';

    // prev link

    if ( $prev_page )
    {
        printf( '<li class="page-item page-item-prev"><a href="#" class="page-link" tabindex="-1" data-paged="%1$d" aria-label="%1$s"><span aria-hidden="true">&laquo;</span><span class="sr-only">%2$s</span></a></li>', 
            $prev_page, esc_attr__( 'Previous' ) );
    }

    // pagination

    for ( $n = 1; $n <= $total; $n++ )
    {
        $class = $paged == $n ? ' active' : '';

        $text = $n;

        if ( $paged == $n ) 
        {
            $text = sprintf( '%d <span class="sr-only">%s</span>', $n, esc_html__( 'current' ) );
        }

        printf( '<li class="page-item%1$s"><a href="#" class="page-link" data-paged="%2$d">%3$s</a></li>', 
            $class, $n, $text );
    }

    // next link

    if ( $next_page )
    {
        printf( '<li class="page-item page-item-prev"><a href="#" class="page-link" tabindex="-1" data-paged="%1$d" aria-label="%1$s"><span aria-hidden="true">&raquo;</span><span class="sr-only">%2$s</span></a></li>', 
            $next_page, esc_attr__( 'Next' ) );
    }

    echo '</ul>'; // .pagination

    echo '</nav>'; // .page-navigation
}

function pb_get_column_class( $data, $fallback = 'col' )
{
	$breakpoints = pb()->options->get( 'grid_breakpoint_formats' );

	$classes = array();

	foreach ( $data as $breakpoint => $cols ) 
	{
		if ( ! isset( $breakpoints[ $breakpoint ] ) ) 
		{
			continue;
		}

		if ( ! $cols ) 
		{
			continue;
		}

		$format = $breakpoints[ $breakpoint ];

		$classes[ $breakpoint ] = sprintf( $format, 'col', $cols );
	}

	if ( ! $classes && $fallback ) 
	{
		$classes[] = $fallback;
	}

	return implode( ' ', $classes );
}

function pb_get_column_choices( $breakpoint = null )
{
	$choices = pb()->options->get( 'columns' );

	if ( $breakpoint && $breakpoint != 'xs' ) 
	{
		// xs, sm, md, lg, xl

		return array
		(
			'' => PB_INHERIT_FROM_SMALLER
		) + $choices;
	}

	return $choices;
}

function pb_get_image_size_choices()
{
	$names = (array) apply_filters( 'image_size_names_choose', array
	(
		'thumbnail' => __( 'Thumbnail' ), 
		'medium'    => __( 'Medium' ), 
		'large'     => __( 'Large' )
	));

	$choices = array();

	$sizes = pb_get_image_sizes();

	foreach ( $names as $size => $name ) 
	{
		if ( ! isset( $sizes[ $size ] ) ) 
		{
			continue;
		}

		$data = $sizes[ $size ];

		$choices[ $size ] = sprintf( '%s - %d x %d', $name, $data['width'], $data['height'] );
	}

	$choices['full'] = __( 'Full Size' );

	return $choices;
}

// https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
function pb_get_image_sizes() 
{
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) 
	{
		// Core

		if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) 
		{
			$sizes[ $_size ] = array
			(
				'width'  => get_option( "{$_size}_size_w" ),
				'height' => get_option( "{$_size}_size_h" ),
				'crop'   => (bool) get_option( "{$_size}_crop" ),
			);
		}

		// Custom

		elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) 
		{
			$sizes[ $_size ] = array
			(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}

function pb_dropdown_options( $options, $selected = '' )
{
	$selected = array_map( 'strval', (array) $selected );

	foreach ( (array) $options as $option_value => $option_text ) 
	{
		$is_selected = in_array( strval( $option_value ), $selected );

		printf( '<option value="%s"%s>%s</option>', 
			esc_attr( $option_value ),
			selected( $is_selected, true, false ),
			esc_html( $option_text )
		);
	}
}

function pb_render_attributes( $attributes, $extra = '' )
{
	$str = '';

	foreach ( $attributes as $name => $value ) 
	{
		$str .= sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) );
	}

	if ( $extra ) 
	{
		$str .= " $extra";
	}

	return $str;
}

function pb_stripcslashes( &$data )
{
	if ( is_array( $data ) )
	{
		foreach ( $data as $key => &$value ) 
		{
			pb_stripcslashes( $value );
		}
	}

	else
	{
		$data = stripcslashes( $data );
	}
}

function pb_sanitize_html_class( $class )
{
	$classes = explode( ' ', $class );

	$sanitized = array();

	foreach ( $classes as $class ) 
	{
		$class = sanitize_html_class( $class );

		if ( ! $class ) 
		{
			continue;
		}

		// Sets class as key to prevent duplicates.
		$sanitized[ $class ] = $class;
	}

	return implode( ' ', $sanitized );
}

function pb_sort_order( $a, $b )
{
	if ( $a['order'] == $b['order'] ) 
	{
    	return 0;
	}
	
	return ( $a['order'] < $b['order'] ) ? -1 : 1;
}

// https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices#Parameters
function pb_notice( $message, $type = 'info', $echo = true ) 
{
	$class = sprintf( 'pb-notice notice notice-%s', sanitize_html_class( $type ) );

	$notice = sprintf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );

	if ( ! $echo ) 
	{
		return $notice;
	}
	
	echo $notice;
}