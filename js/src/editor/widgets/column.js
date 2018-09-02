(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var column = pb.widget.extend( 
	{
		init : function()
		{
			this._super( 'column' );

			pb.addAction( 'load', this.load );
		},

		load : function()
		{
			pb.$elem.find( '.pb-main-widget-container .pb-row-widget' ).each( function()
			{
				var $columns = $( this ).find( '> .pb-widget-inside > .pb-widget-container' ).children();

				if ( $columns.length == 1 ) 
				{
					$columns.find( '> .pb-widget-top .pb-widget-delete-control' ).hide();
				}
			});
		},

		widget : function( $widget )
		{
			$widget.find( '> .pb-widget-inside > .pb-widget-container' ).sortable( 
			{
				handle : '> .pb-widget-top',
				placeholder: 'pb-sortable-placeholder',
				items: '> .pb-widget',
				cursor: 'move',
				distance: 2,
				tolerance: 'pointer',
				refreshPositions: true,
				forcePlaceholderSize : true,
				// Connects with other columns
				connectWith : [ '.pb-column-widget > .pb-widget-inside > .pb-widget-container' ]
			});

			pb.widgets.column.updateCSSClasses( $widget );
		},

		widgetAdd : function( $widget )
		{
			pb.widgets.column.updateCSSClasses( $widget );

			if ( $widget.siblings().length ) 
			{
				$widget.siblings().find( '> .pb-widget-top .pb-widget-delete-control' ).show();
			}
		},

		widgetRemove : function( $widget, model, $parentWidget, index )
		{
			var $columns = $parentWidget.find( '> .pb-widget-inside > .pb-widget-container' ).children();

			if ( $columns.length == 1 ) 
			{
				$columns.find( '> .pb-widget-top .pb-widget-delete-control' ).hide();
			}
		},

		widgetUpdate : function( $widget )
		{
			pb.widgets.column.updateCSSClasses( $widget );
		},

		updateCSSClasses : function( $widget )
		{
			var model = pb.models[ $widget.data( 'model' ) ];

			// Removes previous set classes

			$widget.removeClass( function( i, className )
			{
				var classNames = className.split( ' ' ), remove = [];

				jQuery.each( classNames, function()
				{
					if ( /^(pb-col|pb-offset)(-(sm|md|lg|xl))?(-\d+)?$/.test( this ) ) 
					{
						remove.push( this );
					}
				});

				return remove.join( ' ' );
			});

			// Adds classes

			// Merges data (width md is in other param)
			var data = jQuery.extend( 
			{
				width_md : model.data.width || 12
			}, model.data.responsiveness );

			if ( data.offset_md ) 
			{
				$widget.addClass( 'pb-offset-md-' + data.offset_md );
			}

			if ( data.width_md ) 
			{
				$widget.addClass( 'pb-col-md-' + data.width_md );
			}

			else
			{
				$widget.addClass( 'pb-col-md-12' );
			}
		}
	});

	pb.widgets.column = new column();

})( jQuery );