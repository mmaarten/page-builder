(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var icon = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'icon' );
		},

		field : function( $field )
		{
			$field.on( 'click', '.pb-icon-set', function( event )
			{
				$.ajax(
				{
					url : ajaxurl,
					method : 'POST',
					data : pb.prepareAjax(
					{
						action : 'pb_icon_picker',
						active : $field.find( ':input' ).val()
					}),
					success : function( content )
					{
						$.featherlight( content, 
						{
							namespace : 'pb-modal',
							afterContent : function()
							{
								var modal = this;

								this.$content.on( 'input', '.pb-icon-picker-search', function( event )
								{
									var search = $( this ).val();

									search = $.trim( search );
									search = search.toLowerCase();

									modal.$content.find( '.pb-icon-picker-icon' ).show();

									if ( search ) 
									{
										var $exclude = modal.$content.find( '.pb-icon-picker-icon' ).filter( function()
										{
											var term = $( this ).data( 'term' ) || '';

											return term.indexOf( search ) == -1;
										
										}).hide();
									}
								});

								this.$content.on( 'click', '.pb-icon-picker-icon', function()
								{
									var id = $( this ).data( 'id' );
									var html = $( this ).html();

									$field.find( ':input' ).val( id );
									$field.find( '.pb-icon-preview' ).html( html );

									$field.addClass( 'pb-has-value' );

									modal.close();
								});
							}
						});
					}
				})
			});

			$field.on( 'click', '.pb-icon-unset', function( event )
			{
				$field.find( ':input' ).val( '' );
				$field.find( '.pb-icon-preview' ).html( '' );

				$field.removeClass( 'pb-has-value' );
			});
		}
	});

	pb.fields.icon = new icon();

})( jQuery );