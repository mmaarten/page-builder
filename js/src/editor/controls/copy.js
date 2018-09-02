(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var copy = pb.control.extend( 
	{
		init : function()
		{
			this._super( 'copy' );
		},

		click : function( $control, $widget )
		{
			var $duplicate = pb.duplicateWidget( $widget );
		
			$duplicate.insertAfter( $widget );
		}
	});

	pb.controls.copy = new copy();

})( jQuery );