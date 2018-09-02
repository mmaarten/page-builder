(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var post = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'post' );
		},

		field : function( $field )
		{
			$field.find( 'select' ).select2(
			{
				ajax: 
				{
					url      : window.ajaxurl,
					dataType : 'json',
					delay    : 250,
					method   : 'POST',

					data: function ( params ) 
					{
						var $select = jQuery( this.context );

						return pb.prepareAjax(
						{
							action : 'pb_post_field_get_choices',
							field  : $field.data( 'key' ),
							page   : $field.data( 'page' ),
							search : params.term,
							paged  : params.page
						});
					},

					processResults: function ( data, params ) 
					{
						return {
							results : data.items,
							pagination:
							{
								more: data.paged < data.max_num_pages
					        }
						};
					}
				}
			});
		}
	});

	pb.fields.post = new post();

})( jQuery );