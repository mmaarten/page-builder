/**
 * Tab Field
 */
(function( $ )
{
	pb.addAction( 'widgetSettings', function( $content, $widget )
	{
		$content.find( '.pb-fields, .pb-sub-fields' ).each( function()
		{
			var $wrap = $( this );

			var $fields = $wrap.find( '> .pb-field' );

			var $tabFields = $fields.filter( '[data-type="tab"]');

			if ( ! $tabFields.length ) 
			{
				return true;
			};

			$fields.hide();

			var setActiveTab = function( index )
			{
				var $active = $nav.find( '.active' );

				if ( $active.length ) 
				{
					$active.removeClass( 'active' )
						.data( 'field' ).nextUntil( '[data-type="tab"]' )
							.hide();
				};

				var $tab = $nav.find( '.pb-nav-item' ).eq( index );

				$tab.addClass( 'active' )
					.data( 'field' ).nextUntil( '[data-type="tab"]' )
						.show();
			};

			var $nav = $( '<nav class="pb-nav"></nav>' );

			$tabFields.each( function()
			{
				var $field = $( this );
				var $tab   = $( '<a href="#" class="pb-nav-item pb-nav-link"></a>' );

				$tab
					.text( $field.find( '> .pb-label' ).text() )
					.data( 'field', $field )
					.on( 'click', function( event )
					{
						event.preventDefault();

						setActiveTab( $( this ).index() );
					});

				$nav.append( $tab );
			});

			var $field = $( '<div class="pb-field" data-type="tabnav"></div>' );
			$field.append( $nav );
			$field.insertBefore( $tabFields.first() );

			setActiveTab( 0 );
		});
	});

})( jQuery, window, undefined );