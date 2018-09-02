(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	pb.widget = Class.extend(
	{
		id : null,

		init : function( id )
		{
			this.id = id;

			pb.addAction( 'widget/type=' + this.id 		     , this.widget );
			pb.addAction( 'widgetAdd/type=' + this.id        , this.widgetAdd );
			pb.addAction( 'widgetUpdate/type=' + this.id     , this.widgetUpdate );
			pb.addAction( 'widgetRemove/type=' + this.id     , this.widgetRemove );
			pb.addAction( 'widgetSettings/type=' + this.id   , this.widgetSettings );
			pb.addAction( 'widgetSettingsSubmit/type=' + this.id, this.widgetSettingsSubmit );
			pb.addAction( 'widgetSortUpdate/type=' + this.id , this.widgetSortUpdate );
			pb.addAction( 'widgetSortReceive/type=' + this.id, this.widgetSortReceive );
			pb.addAction( 'widgetSortRemove/type=' + this.id , this.widgetSortRemove );
		},

		widget : function( $widget )
		{
			
		},

		widgetAdd : function( $widget )
		{
			
		},

		widgetUpdate : function( $widget )
		{
			
		},

		widgetRemove : function( $widget, model, $parentWidget, index )
		{
			
		},

		widgetSettings : function( $widget, $content )
		{
			
		},

		widgetSettingsSubmit : function( $widget, $content )
		{

		},

		widgetSortUpdate : function ( $widget, $container )
		{

		},

		widgetSortReceive : function ( $widget, $container )
		{
			
		},

		widgetSortRemove : function ( $widget, $container )
		{
			
		}
	});

	pb.widgets = {};

})( jQuery );