/**
 * Fields
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	pb.fields = {};
	
	pb.field = 
	{
		id : null,

		extend : function( options )
		{
			$.extend( this, options );

			pb.addAction( 'field/type=' + this.id, this.field );
		},

		field : function( $field )
		{

		},
	};

	var models = {};

	function init()
	{
		pb.addAction( 'load', load );
		pb.addAction( 'widgetSettings', initFields );
	}

	pb.addAction( 'init', init );

	function load( response )
	{
		models = response.fields;
	}

	function initFields( $content )
	{
		$content.find( '.pb-field' ).each( function()
		{
			var $field = $( this );

			pb.doAction( 'field'                              , $field );
			pb.doAction( 'field/type=' + $field.data( 'type' ), $field );
		});
	}

	function getFieldModel( $field )
	{
		var key = $field.data( 'key' );

		return models[ key ];
	}

	pb.getFieldModel = getFieldModel;

})( jQuery, window, undefined );
