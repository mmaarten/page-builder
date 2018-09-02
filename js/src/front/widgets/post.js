(function( $ )
{
	function Plugin( elem, options )
	{
		var _this = this;

		this.$elem   = $( elem );
		this.options = $.extend( {}, Plugin.defaults, options );

		this.$elem.on( 'click', '.pagination .page-link', function( event )
		{
			event.preventDefault();

			_this.load(
			{
				pageIndex : $( this ).data( 'paged' ),
				animate : true
			});
		});

        jQuery( document ).trigger( 'pb/postGrid/init', [ this ] );

        this.load();
	};

	Plugin.defaults = 
	{
		ajaxurl : '',
		animationSpeed : 300
	};

	Plugin.prototype.trigger = function( eventType )
	{	
		arguments[0] = 'pb/postGrid/' + eventType;

		this.$elem.trigger.apply( this.$elem, arguments );
	};

	Plugin.prototype.load = function( options )
	{
		var _this = this,
			defaults = 
			{
				pageIndex : 1,
				animate : false
			};

		var options = $.extend( {}, defaults, options );

		// Sets page index
		this.$elem.find( ':input[name="paged"]' ).val( options.pageIndex );

		this.$elem.addClass( 'loading' );

		this.trigger( 'loadBefore' );

		var data = this.$elem.find( 'form' ).serialize();

		$.ajax(
		{
			url : this.options.ajaxurl,
			method : 'POST',
			data : data,
			success : function( response )
			{
				if ( ! response.success ) 
				{
					return;
				}

				var data = $.extend( {}, response.data );

				_this.$elem.find( '.pb-entries' ).html( data.content );

				// Animation

				if ( options.animate ) 
				{
					$( 'html, body' ).stop().animate(
					{
						scrollTop : _this.$elem.offset().top - 100
					}, _this.options.animationSpeed );
				};

				//

				_this.trigger( 'loadSuccess' );
			},
			complete : function()
			{
				_this.$elem.removeClass( 'loading' );

				_this.trigger( 'loadComplete' );
			}
		});
	}

	$.fn.postGrid = function( options )
	{
		return this.each( function()
		{
			if ( $( this ).data( 'postGrid' ) ) 
			{
				return true;
			};

			var instance = new Plugin( this, options );

			$( this ).data( 'postGrid', instance );
		});
	};

	window.postGrid = Plugin;

})( jQuery );