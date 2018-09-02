(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	pb.control = Class.extend(
	{
		id : null,

		init : function( id )
		{
			this.id = id;

			pb.addAction( 'controlClick/type=' + this.id, this.click );
		},

		click : function( $control, $widget )
		{
			
		}
	});

	pb.controls = {};

})( jQuery );