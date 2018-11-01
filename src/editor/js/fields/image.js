/**
 * Image Field
 */
(function( $, window, undefined )
{
	"use strict";

	var image = pb.field.extend(
	{
		id : 'image',

		field : function( $field )
		{
			console.log( $field.get(0) );
		},
	});

	pb.fields.image = image;

})( jQuery, window, undefined );
