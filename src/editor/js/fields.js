/**
 * Fields
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	pb.field = 
	{
		id : null,

		extend : function( options )
		{
			$.extend( this, options );

			pb.hooks.addAction( 'field/type=' + this.id, this.field );
		},

		field : function( $field )
		{
			
		},
	};

	pb.fields = {};

	$( document ).on( 'ready', function()
	{
		$( '.pb-fields > .pb-field' ).each( function()
		{
			var $field = $( this );

			pb.hooks.doAction( 'field', $field );
			pb.hooks.doAction( 'field/type=' + $field.data( 'type' ), $field );
			pb.hooks.doAction( 'field/key=' + $field.data( 'key' ), $field );
		});
	});

})( jQuery, window, undefined );
