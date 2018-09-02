(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var add = pb.control.extend( 
	{
		init : function()
		{
			this._super( 'add' );
		},

		click : function( $control, $widget )
		{
			pb.widgetPicker( function( $choosen )
			{
				$widget.find( '> .pb-widget-inside > .pb-widget-container' ).append( $choosen );

				pb.doWidgetAction( 'widgetAdd', $choosen );
				pb.loadWidgetPreview( $choosen );

			}, $widget );
		}
	});

	pb.controls.add = new add();

})( jQuery );