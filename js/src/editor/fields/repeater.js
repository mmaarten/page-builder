(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var repeater = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'repeater' );
		},

		field : function( $field )
		{
			var $elem = $field.find( '> .pb-input > .pb-repeater' );

			var $rows = $elem.find( '> .pb-repeater-table > .pb-repeater-rows' );

			var i = $rows.find( '> .pb-repeater-row' ).length - 1;

			// Init sub fields
			$rows.find( '> .pb-repeater-row:not(.pb-clone) > .pb-field' ).each( function()
			{
				pb.fields.doFieldAction( 'field', $( this ) );
			});

			$rows.sortable(
			{
				items: '> .pb-repeater-row:not(.pb-clone)',
				cursor: 'move',
				distance: 2,
				tolerance: 'pointer',
				refreshPositions: true,
				forcePlaceholderSize : true
			});

			$elem.on( 'click', '> .pb-repeater-footer .pb-repeater-add', function( event )
			{
				// Creates row

				var $row = $rows.find( '> .pb-repeater-row.pb-clone' ).clone( false ).removeClass( 'pb-clone' );

				i++;

				// Updates row input fields

				$row.find( ':input' ).each( function()
				{
					var $input = $( this );

					var replacements = 
					[
						{ attr : 'name', find : '[0]', replacement : '[' + i + ']' },
						{ attr : 'id'  , find : '-0-', replacement : '-' + i + '-' }
					];

					$.each( replacements, function()
					{
						var value = $input.attr( this.attr );

						if ( typeof value === 'undefined' ) 
						{
							return true;
						}

						var pos = value.indexOf( this.find );

						if ( pos == -1 ) 
						{
							return true;
						}

						var before = value.substring( 0, pos );
						var after  = value.substring( pos + this.find.length );

						var newValue = before + this.replacement + after;

						$input.attr( this.attr, newValue );
					});
				});

				// Adds row to DOM

				$rows.append( $row );

				// Init sub fields

				$row.find( '> .pb-field' ).each( function()
				{
					pb.fields.doFieldAction( 'field', $( this ) );
				});
			});

			$elem.on( 'click', '.pb-repeater-remove', function( event )
			{
				var $row = jQuery( this ).closest( '.pb-repeater-row' );

				$row.remove();
			});
		}
	});

	pb.fields.repeater = new repeater();

})( jQuery );