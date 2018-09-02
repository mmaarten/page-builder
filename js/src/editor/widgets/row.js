(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var row = pb.widget.extend( 
	{
		init : function()
		{
			this._super( 'row' );

			pb.addAction( 'widgetAdd', this.anyWidgetAdded );
		},

		getLayout : function( $widget, doFractions )
		{
			var layout = [];

			$widget.find( '> .pb-widget-inside > .pb-widget-container > .pb-column-widget' ).each( function()
			{
				var $column = $( this ),
				model = pb.models[ $column.data( 'model' ) ];

				var width = model.data.width || 12;

				if ( doFractions ) 
				{
					var fraction = pb.reduceFraction( model.data.width || 12, 12 );

					width = fraction[0] + '/' + fraction[1];
				}

				layout.push( width );
			});

			return layout;
		},

		setLayout : function( layout, $widget )
		{
			var current = this.getLayout( $widget, false );

			if ( current.join('+') == layout.join('+') ) 
			{
				return;
			}

			/**
			 * Removes columns
			 * -------------------------------------------------------
			 */

			$widget.find( '> .pb-widget-inside > .pb-widget-container' ).children().filter( function()
			{
				return jQuery( this ).index() > layout.length - 1;
			})

			.each( function()
			{
				pb.removeWidget( this );
			});

			/**
			 * Creates/updates columns
			 * -------------------------------------------------------
			 */

			jQuery.each( layout, function( i, width )
			{
				var $column = $widget.find( '> .pb-widget-inside > .pb-widget-container' ).children().eq( i );

				// create

				if ( ! $column.length ) 
				{
					$column = pb.createWidget(
					{
						type : 'column',
						data : { width : width }
					});

					$widget.find( '> .pb-widget-inside > .pb-widget-container' )
						.append( $column );

					pb.doWidgetAction( 'widgetAdd', $column );
				}

				// update

				else
				{
					var m = pb.models[ $column.data( 'model' ) ];

					// clears offset
					m.data.responsiveness = jQuery.extend( {}, m.data.responsiveness, { offset_md : '' } );
					
					m.data.width = width;

					console.log( width );

					pb.doWidgetAction( 'widgetUpdate', $column );
				};
			});
		},

		widget : function( $widget )
		{
			$widget.find( '> .pb-widget-inside > .pb-widget-container' )
				.addClass( 'pb-row' )
				.sortable( 
				{
					handle : '.pb-widget-top',
					placeholder: 'pb-sortable-placeholder',
					items: '> .pb-widget',
					cursor: 'move',
					distance: 2,
					tolerance: 'pointer',
					refreshPositions: true,
					forcePlaceholderSize : true
				});
		},

		anyWidgetAdded : function( $widget )
		{
			if ( $widget.data( 'type' ) == 'row' ) 
			{
				return;
			}

			var $parentWidget = $widget.parent().closest( '.pb-widget' );

			if ( $parentWidget.length ) 
			{
				return;
			}

			pb.removeAction( 'widgetAdd', pb.widgets.row.anyWidgetAdded );

			var $parent = $widget.parent();
			// TODO : $index  = $widget.index();

			var $row = pb.createWidget( 'row' );

			if ( $widget.data( 'type' ) == 'column' ) 
			{
				$row.find( '> .pb-widget-inside > .pb-widget-container' )
					.append( $widget );
			}

			else
			{
				var $column = pb.createWidget( 'column' );

				$row.find( '> .pb-widget-inside > .pb-widget-container' )
					.append( $column );

				$column.find( '> .pb-widget-inside > .pb-widget-container' )
					.append( $widget );
			}

			$parent.append( $row );

			pb.addAction( 'widgetAdd', pb.widgets.row.anyWidgetAdded );
		},

		widgetAdd : function( $widget )
		{
			// Adds column when empty

			if ( ! $widget.find( '.pb-widget' ).length ) 
			{
				var $column = pb.createWidget( 'column' );

				console.log( pb.models[ $column.data( 'model' ) ] );

				$widget.find( '> .pb-widget-inside > .pb-widget-container' ).append( $column );

				pb.doWidgetAction( 'widgetAdd', $column );
			}
		},

		widgetSettings : function( $widget, $content )
		{
			var picker = wp.template( 'pb-row-layout-picker' )();

			jQuery( picker ).insertBefore( $content.find( ':input#pb-input-layout' ) );

			/**
			 * Layout Picker
			 * -------------------------------------------------------
			 */

			(function()
			{
				var $picker = $content.find( '.pb-layout-picker' );
				var $target = jQuery( $picker.data( 'target' ) );

				function setLayout( layout )
				{
					$target.val( layout );

					var $button = $picker.find( 'button' ).filter( function()
					{
						return jQuery( this ).data( 'layout' ) == layout;
					});

					$picker.find( 'button' )
						.removeClass( 'active' );

					$button.addClass( 'active' );
				};

				$picker.on( 'click', 'button', function( event )
				{
					var layout = jQuery( this ).data( 'layout' );

					setLayout( layout );
				});

				$target.on( 'change', function( event )
				{
					var layout = jQuery( this ).val();

					setLayout( layout );
				});

				setLayout( $target.val() );

			})();

			/**
			 * Populates Layout field
			 * -------------------------------------------------------
			 */

			var layout = pb.widgets.row.getLayout( $widget, true );

			$content.find( ':input#pb-input-layout' )
				.val( layout.join( '+' ) )
				.trigger( 'change' )
		},

		widgetSettingsSubmit : function( $widget, $content )
		{
			/**
			 * Layout
			 * -------------------------------------------------------
			 */

			var layout = $content.find( ':input#pb-input-layout' ).val();

			layout = pb.widgets.row.sanitizeLayout( layout );

			pb.widgets.row.setLayout( layout.split( '+' ), $widget );
		},

		sanitizeLayout : function( layout )
		{
			var min = 1, max = 12;

			layout = layout.replace( /\++/g, '+' ); // removes double '+'
			layout = layout.replace( /(^\+)|(\+$)/g, '' ); // removes starting and ending '+'
			layout = layout.replace( /\s/g, '' ); // removes spaces

			if ( ! layout )
			{
				return String( max );
			};

			layout = layout.split( '+' );

			var sanitized = [];

			jQuery.each( layout, function( i, cols )
			{
				// converts fractions to numbers

				var matches = cols.match( /^(\d+)\/(\d+)$/ );

				if ( matches ) 
				{
					var numerator   = parseInt( matches[1] );
					var denominator = parseInt( matches[2] );

					cols = max * ( numerator / denominator );
				};

				// Checks if integer

				if ( cols != parseInt( cols, 10 ) )
				{
					cols = max;
				};

				// Checks range

				if ( cols < min ) 
				{
					cols = min;
				}

				else if ( cols > max )
				{
					cols = max;
				};

				sanitized.push( cols );
			});

			return sanitized.join( '+' );
		}
	});

	pb.widgets.row = new row();

})( jQuery );