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

function pb_image_preview( $image_url )
{
	?>

	<div class="wp-core-ui">
		<div class="attachment" style="float:none; padding: 0; max-width: 100px;">
			<div class="attachment-preview">
				<div class="thumbnail">
					<div class="centered">
						<img class="pb-media-picker-image" src="<?php echo esc_url( $image_url ); ?>">
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
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

/**
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
 */
function pb_admin_notice( $message, $type = 'info' ) 
{
	$class = "pb-notice notice notice-$type";

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message ); 
}
