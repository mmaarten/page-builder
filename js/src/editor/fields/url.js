(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var urlField = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'url' );
		},

		field : function( $field )
		{
			$field.find( ':input[data-search="1"]' ).autocomplete(
			{
				minLength: 1,
				source: function( request, response ) 
				{
					$.ajax( 
					{
						url: ajaxurl,
						method : 'POST',
						data: pb.prepareAjax(
						{
							action : 'pb_url_autocomplete',
							term: request.term,
							field : $field.data( 'key' ),
							page  : $field.data( 'page' )
						}),
						success: function( data ) 
						{
							response( data );
						}
					});
				}
			})

			.autocomplete( 'instance' )._renderItem = function( ul, item ) 
			{
				var $li = $( '<li>' );

				var $title = $( '<strong></strong>' ).text( item.label );
				var $description = $( '<em></em>' ).text( item.url );

				$li.append( $title );
				$li.append( '<br>' );
				$li.append( $description );

				return $li.appendTo( ul );
		    }
		}
	});

	pb.fields.url = new urlField();

})( jQuery );