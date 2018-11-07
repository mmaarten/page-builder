/**
 * Editor Field
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};
	
	var editor = pb.field.extend(
	{
		id : 'editor',

		field : function( $field )
		{
			var model = pb.getFieldModel( $field );

			if ( typeof tinymce === 'undefined' )
			{
				return false;
			};

			if ( typeof tinyMCEPreInit === 'undefined' )
			{
				return false;
			};

			var defaults = tinyMCEPreInit.mceInit.pb_content;

			// makes sure we have a valid id (no dashes)

			var $textarea = $field.find( 'textarea' );

			var newId = $textarea.attr( 'id' ).replace( /-/g, '' );

			$textarea.attr( 'id', newId );

			// removes instance if exists

			var instance = tinymce.get( newId );

			if ( instance ) 
			{
				tinymce.execCommand( 'mceRemoveControl', true, newId );

				instance.destroy();
			};

			// mce init

			var init = $.extend( {}, defaults, model.tinymce );

			init.id = newId;
			init.selector = '#' + newId;

			// Stores settings
			tinyMCEPreInit.mceInit[ newId ] = init;

			tinymce.init( init );
		},
	});

	pb.fields.editor = editor;

})( jQuery, window, undefined );
