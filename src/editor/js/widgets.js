/**
 * Widgets
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	pb.widgets = {};

	pb.widget = 
	{
		id : null,

		extend : function( options )
		{
			$.extend( this, options );

			pb.addAction( 'widget/type=' + this.id         , this.widget );
			pb.addAction( 'widgetAdded/type=' + this.id    , this.widgetAdded );
			pb.addAction( 'widgetUpdated/type=' + this.id  , this.widgetUpdated );
			pb.addAction( 'widgetRemoved/type=' + this.id  , this.widgetRemoved );
			pb.addAction( 'widgetSettings/type=' + this.id , this.widgetSettings );
			pb.addAction( 'widgetSortStart/type=' + this.id, this.widgetSortStart );
			pb.addAction( 'widgetSortStop/type=' + this.id , this.widgetSortStop );
		},

		widget : function( $widget )
		{

		},

		widgetAdded : function( $widget )
		{

		},

		widgetUpdated : function( $widget )
		{

		},

		widgetRemoved : function( $widget )
		{

		},

		widgetSettings : function( $content, $widget )
		{

		},

		widgetSortStart : function( $widget, ui )
		{
			
		},

		widgetSortStop : function( $widget, ui )
		{

		},
	};

})( jQuery, window, undefined );
