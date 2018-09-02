(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var editor = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'editor' );
		},

		field : function( $field )
		{
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

			var fieldOptions = $.extend( {}, pb.get( 'editor_field_' + $field.data( 'key' ) ) );


			// mce init

			var init = $.extend( {}, defaults, fieldOptions );

			init.id = newId;
			init.selector = '#' + newId;

			// Stores settings
			tinyMCEPreInit.mceInit[ newId ] = init;

			tinymce.init( init );
		},

		updateInput : function( $field )
		{
			// TODO : Calls the save method on ALL editor instances in the collection
			tinymce.triggerSave();
		},

		destroy : function( $field )
		{
			var $textarea = $field.find( 'textarea' );

			var newId = $textarea.attr( 'id' ).replace( /-/g, '' );

			var instance = tinymce.get( newId );

			if ( instance ) 
			{
				//tinymce.execCommand( 'mceRemoveControl', true, newId );

				instance.destroy();

				instance = null;
			};

			delete tinyMCEPreInit.mceInit[ newId ];
		}
	});

	pb.fields.editor = new editor();

})( jQuery );