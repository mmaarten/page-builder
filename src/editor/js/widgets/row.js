/**
 * Row Widget
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	function gcd( a, b )
	{
		return b ? gcd( b, a % b ) : a;
	}

	function reduceFraction( numerator, denominator )
	{
  		var _gcd = gcd( numerator, denominator );
  		
  		return [ numerator / _gcd, denominator / _gcd ];
	}

	function toFraction( numerators, denominator, reduce )
	{
		var numerator, fraction, fractions = [];

		for ( var i in numerators )
		{
			numerator = numerators[i];

			if ( reduce ) 
			{
				fraction = reduceFraction( numerator, denominator );
			}

			else
			{
				fraction = [ numerator, denominator ];
			}

			fractions.push( fraction[0] + '/' + fraction[1] );
		}
		
		return fractions;
	}

	function getLayout( $row )
	{
		var layout = [];

		// Loop columns
		$row.find( '> .pb-widget-inside > .pb-widget-container > .pb-column-widget' ).each( function()
		{
			// Get column width value

			var model = pb.getWidgetModel( this );
			
			// Add to layout
			layout.push( model.data.cols );
		});

		// Return
		return layout;
	}

	function setLayout( layout, $row )
	{
		var $columns = $row.find( '> .pb-widget-inside > .pb-widget-container > .pb-column-widget' );

		$.each( layout, function( i, cols )
		{
			var $column = $columns.eq( i );

			// Create

			if ( ! $column.length ) 
			{
				$column = pb.createWidget( 
				{
					type : 'column',
					data : { cols : cols },
				});

				var $prevColumn = $row.find( '> .pb-widget-inside > .pb-widget-container > .pb-column-widget' ).eq( i - 1 );
				$column.insertAfter( $prevColumn );

				pb.doAction( 'widgetAdded', $column );
				pb.doAction( 'widgetAdded/type=' + $column.data( 'type' ), $column );
			}

			// Update

			else
			{
				var model = pb.getWidgetModel( $column );

				if ( model.data.cols != cols ) 
				{
					model.data.cols = cols;

					pb.models[ model.id ] = model;

					pb.doAction( 'widgetUpdated', $column );
					pb.doAction( 'widgetUpdated/type=' + $column.data( 'type' ), $column );
				};
			}
		});

		// Delete

		$columns.slice( layout.length ).each( function()
		{
			pb.removeWidget( this );
		});
	}

	function parseLayout( string, options )
	{
		// Options

		var defaults = 
		{
			min : 1,
			max : 12,
			sep : '+'
		};

		options = $.extend( {}, defaults, options );

		// Parse

		var layout = [];

		$.each( String( string ).split( options.sep ), function( i, cols )
		{
			// Remove surrounding whitespace
			cols = $.trim( cols );

			// Convert fraction
			if ( /^\d+\/\d+$/.test( cols ) ) 
			{
				var parts = cols.split( '/' );

				cols = options.max * ( parts[0] / parts[1] );
			}

			// Make integer
			cols = parseInt( cols );

			// Check if number
			if ( isNaN( cols ) ) 
			{
				return true;
			}

			// check bounds
			if ( cols < options.min ) 
			{
				cols = options.min;
			}

			else if ( cols > options.max )
			{
				cols = options.max;
			}

			// Add to layout
			layout.push( cols );
		});

		// Return
		return layout;
	}

	var row = pb.widget.extend(
	{
		id : 'row',
		
		widget : function( $widget )
		{
			// Set css class
			$widget.find( '> .pb-widget-inside > .pb-widget-container' )
				.addClass( 'pb-row' );
				
			// Sorting
			$widget.find( '> .pb-widget-inside > .pb-widget-container' ).sortable(
			{
				placeholder: 'pb-widget-placeholder',
				items: '> .pb-widget',
				handle: '> .pb-widget-top > .pb-widget-title',
				cursor: 'move',
				distance: 2,
				containment: '#wpwrap',
				tolerance: 'pointer',
				refreshPositions: true,
			});
		},

		widgetSettings : function( $content, $widget )
		{
			// Layout

			var layout = getLayout( $widget );
			var $field = $content.find( '#pb_input-row_layout' );

			layout = toFraction( layout, 12, true );

			$field.val( layout.join( '+' ) );

			$content.on( 'click', '.pb-layout-control', function( event )
			{
				var layout = $( this ).data( 'layout' );

				layout = parseLayout( layout );
				layout = toFraction( layout, 12, true );

				$field.val( layout.join( '+' ) );
			});
		},

		widgetUpdated : function( $widget )
		{
			var model = pb.getWidgetModel( $widget );

			var layout = parseLayout( model.data.layout );

			// One column is required
			if ( ! layout.length ) 
			{
				layout.push( 12 );
			}

			setLayout( layout, $widget );
		},
	});

	pb.widgets.row = row;

})( jQuery, window, undefined );
