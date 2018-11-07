/**
 * Image Field
 *
 * @link https://codex.wordpress.org/Javascript_Reference/wp.media
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};
	
	var image = pb.field.extend(
	{
		id : 'image',

		field : function( $field )
		{
			var model = pb.getFieldModel( $field );

			console.log( model );

			// Media Picker

			var frame = null,
			$picker        = $field.find( '> .pb-media-picker' ),
			$input         = $picker.find( '.pb-media-picker-input' ),
			$image         = $picker.find( '.pb-media-picker-image' ),
			$addControl    = $picker.find( '.pb-media-picker-add' ),
			$removeControl = $picker.find( '.pb-media-picker-remove' );

			if ( $input.val() ) 
			{
				$picker.addClass( 'pb-has-value' );
			}

			else
			{
				$picker.removeClass( 'pb-has-value' );
			};

			// Add button click
			$addControl.on( 'click', function( event )
			{
				// If the media frame already exists, reopen it.
			    if ( frame ) 
			    {
					frame.open();
			      	
			      	return;
			    };

			    // Create a new media frame
			    frame = wp.media(
			    {
					title    : 'Choose Image',
					button   : { text: 'Insert Image' },
					library  : { type: [ 'image' ] },
					multiple : false
			    })

			    // Image selected
			    .on( 'select', function( event )
			    {
					var attachment = frame.state().get('selection').first().toJSON();

					// Get size data

					var size = attachment.sizes.full;

					if ( attachment.sizes.thumbnail !== undefined ) 
					{
						size = attachment.sizes.thumbnail;
					}

					// Update view

					$input.val( attachment.id );
					$image.attr( 'src', size.url );

					$picker.addClass( 'pb-has-value' );
			    });

			});

			// Remove button click
			$removeControl.on( 'click', function( event )
			{
				$input.val( '' );
				$image.removeAttr( 'src' );

				$picker.removeClass( 'pb-has-value' );
			});
		},
	});

	pb.fields.image = image;

})( jQuery, window, undefined );
