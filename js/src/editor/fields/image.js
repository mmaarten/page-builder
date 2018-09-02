(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var image = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'image' );
		},

		field : function( $field )
		{
			var update = function()
			{
				var $items     = $field.find( '.pb-image-picker-items .pb-image-picker-item' );
				var $addButton = $field.find( '.pb-image-picker-add' );
				var multiple   = $field.find( '.pb-image-picker' ).data( 'multiple' ) ? true : false;

				if ( ! multiple && $items.length ) 
				{
					$addButton.hide();
				}

				else
				{
					$addButton.show();
				};
			};

			var createItem = function( attachment )
			{
				var imageSize;

				if ( typeof attachment.sizes.thumbnail !== 'undefined' ) 
				{
					imageSize = attachment.sizes.thumbnail;
				}

				else
				{
					imageSize = attachment.sizes.full;
				}

				var $item = $field.find( '.pb-clone .pb-image-picker-item' ).clone( false ).removeClass( 'pb-clone' );

				$item.find( ':input' ).val( attachment.id );
				$item.find( 'img' ).attr( 'src', imageSize.url );

				return $item;
			};
			
			var multiple = $field.find( '.pb-image-picker' ).data( 'multiple' ) ? true : false;

			if ( multiple ) 
			{
				$field.find( '.pb-image-picker-items' ).sortable(
				{
					cursor: 'move',
					distance: 2,
					tolerance: 'pointer',
					refreshPositions: true,
					forcePlaceholderSize : true
				});
			}

			var frame, _this = this;

			$field.on( 'click', '.pb-image-picker-add-control', function( event )
			{
				event.preventDefault();

				// If the media frame already exists, reopen it.

			    if ( frame ) 
			    {
			    	frame.open();

			    	return;
			    }

				// Create a new media frame

			    frame = wp.media(
			    {
					title    : 'Choose Image',
					button   : { text: 'Insert Image' },
					library  : { type: [ 'image' ] },
			     	multiple : multiple
			    });
			    
			    // When an image is selected in the media frame...

			    frame.on( 'select', function( event ) 
			    {
					// Get attachments

					var attachments = frame.state().get('selection').toJSON();

					// Creates items and add them to the DOM

					jQuery.each( attachments, function( i, attachment )
					{
						var $item = createItem( attachment );

						$field.find( '.pb-image-picker-items' )
							.append( $item );
					});

					update();
			    });

			    // Opens frame

			    frame.open();
			});

			$field.on( 'click', '.pb-image-picker-delete-control', function( event )
			{
				event.preventDefault();

				var $item = jQuery( this ).closest( '.pb-image-picker-item' );

				$item.remove();

				update();
			});

			update();
		}
	});

	pb.fields.image = new image();

})( jQuery );