/**
 * Column Widget
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	function getGridCSSClasses( $widget )
	{
		var className = $widget.attr( 'class' ) || '';

		/**
		 * Remove all grid related classes
		 */

		var pattern = ''
			+ '\\b'						// boundary
			+ '('						// open capture
			+ 'pb-'						// class prefix
			+ '(?:offset|col|order)'	// property
			+ '(?:-(?:sm|md|lg|xl))?'   // breakpoint (optional)
			+ '(?:-\\d+)?'				// value (optional)
			+ ')'						// close capture
			+ '\\b';					// boundary

		var regExp = new RegExp( pattern, 'g' );
		
		var matches = className.match( regExp );

		return matches ? matches.join( ' ' ) : '';
	}
	
	function updateCSSClass( $widget )
	{
		/**
		 * Remove all grid related classes
		 */
		
		$widget.removeClass( function( index, className )
		{
			return getGridCSSClasses( $widget );
		});

		/**
		 * Add grid classes
		 */

		var model = pb.getWidgetModel( $widget );

		// Offset

		var offset = model.data.responsiveness.offset_sm;

		if ( offset ) 
		{
			$widget.addClass( 'pb-offset-sm-' + offset );
		};

		// Cols

		var cols = model.data.cols;

		if ( cols ) 
		{
			$widget.addClass( 'pb-col-sm-' + cols );
		};

		// Order

		var order = model.data.responsiveness.order_sm;

		if ( order ) 
		{
			$widget.addClass( 'pb-order-sm-' + order );
		};
	}

	var column = pb.widget.extend(
	{
		id : 'column',

		widget : function( $widget )
		{
			updateCSSClass( $widget );

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
				connectWith : '.pb-column-widget > .pb-widget-inside > .pb-widget-container',
			});
		},

		widgetUpdated : function( $widget )
		{
			updateCSSClass( $widget );
		},

		widgetSortStart : function( $widget, ui )
		{
			// Adds grid related classes to placeholder
			var className = getGridCSSClasses( $widget );

			ui.placeholder.addClass( className );
		},

		widgetSortStop : function( $widget, ui )
		{
			// Removes grid related classes to placeholder
			var className = getGridCSSClasses( $widget );

			ui.placeholder.removeClass( className );
		},
	});

	pb.widgets.column = column;

})( jQuery, window, undefined );
