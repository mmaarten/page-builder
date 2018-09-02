(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var del = pb.control.extend( 
	{
		init : function()
		{
			this._super( 'delete' );
		},

		click : function( $control, $widget )
		{
			var message = pb.get( 'confirmDelete' );

			if ( message && ! window.confirm( message ) ) 
			{
				return;
			}

			$widget.find( '> .pb-widget-inside' ).slideUp( 'fast', function()
			{
				$widget.fadeOut( 'fast', function()
				{
					pb.removeWidget( $widget );
				});
			});
		}
	});

	pb.controls.delete = new del();

})( jQuery );