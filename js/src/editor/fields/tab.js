(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	pb.addAction( 'widgetSettings', function( $widget, $page )
	{
		$page.find( '.pb-fields, .pb-sub-fields' ).each( function()
		{
			// selects all children from the first tab
			// uses children() cause there can be other elements than '.pb-field'

			var $fields = $( this ).children().filter( '.pb-field-type-tab' ).first().nextAll().andSelf();

			if ( ! $fields.length ) 
			{
				return true;
			}

			var $tabFields = $fields.filter( '.pb-field-type-tab' );

			var setActiveTab = function( tab )
			{
				var $tab = $( tab );

				if ( $tab.is( '.nav-tab-active' ) ) 
				{
					return;
				};

				// Selects tab field and other fields until next tab field.

				var $_fields = $fields.filter( function()
				{
					return $( this ).data( 'type' ) == 'tab' 
					    && $( this ).data( 'key' ) == $tab.data( 'key' );

				}).nextUntil( '.pb-field[data-type="tab"]' );

				$_fields.show();

				$fields.not( $_fields ).hide();

				// Updates nav

				$nav.find( '.nav-tab-active' ).removeClass( 'nav-tab-active' );

				$tab.addClass( 'nav-tab-active' );
			};

			// Tab Nav

			var $nav = $( '<h2 class="nav-tab-wrapper"></h2>' );

			$tabFields.each( function()
			{
				var $field = $( this );

				var $item = $( '<a class="nav-tab"></a>' );

				$item
					.text( $field.find( '.pb-input-label' ).text() )
					.attr( 'href', '' ) // cursor pointer on hover
					.attr( 'data-key', $field.data( 'key' ) )

				$nav.append( $item );
			});

			$nav.on( 'click', '.nav-tab', function( event )
			{
				event.preventDefault();

				setActiveTab( this );
			});

			// Inserts nav just before the first tab field

			$nav.insertBefore( $tabFields.first() );

			// Activates first tab

			setActiveTab( $nav.find( '.nav-tab' ).first() );
		});
	});
})( jQuery );
