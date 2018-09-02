(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var edit = pb.control.extend( 
	{
		init : function()
		{
			this._super( 'edit' );
		},

		click : function( $control, $widget )
		{
			pb.widgetSettings( $widget );
		}
	});

	pb.controls.edit = new edit();

})( jQuery );