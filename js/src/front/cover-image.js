/**
 * Cover Image
 * ---------------------------------------------------------------
 */
(function( $ )
{
	jQuery( document ).ready( function()
	{
		// Sets image as background image

		$( '.pb-browser-ie .pb-cover-image' ).each( function()
		{
			var $img = $( this ).find( 'img' );

			$( this )
				.css( 'background-image', 'url(' + $img.attr( 'src' ) + ')' );
		});
	});
})( jQuery );