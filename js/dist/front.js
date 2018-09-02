/**
 * Common
 * ---------------------------------------------------------------
 */
(function( $ )
{
	jQuery( document ).ready( function()
	{
		$( 'body' )
			.removeClass( 'pb-no-js' )
			.addClass( 'pb-js' );
	});
})( jQuery );

/**
 * Cover Image
 * ---------------------------------------------------------------
 */
(function( $ )
{
	jQuery( document ).ready( function()
	{
		// Sets image as background image

		$( '.pb-browser-ie .pb-cover-image' ).each( function()
		{
			var $img = $( this ).find( 'img' );

			$( this )
				.css( 'background-image', 'url(' + $img.attr( 'src' ) + ')' );
		});
	});
})( jQuery );
(function( $ )
{
	function Map( canvas, options )
	{
		var _this = this;

		this.$canvas = jQuery( canvas );
		this.options = jQuery.extend( {}, Map.defaults, options );

		this.markers    = [];
		this.bounds     = new google.maps.LatLngBounds();
		this.infowindow = new google.maps.InfoWindow();

		/**
         * Map
         * -----------------------------------------------------------
         */

		this.map = new google.maps.Map( this.$canvas.get( 0 ) );

        /**
         * Markers
         * -----------------------------------------------------------
         */

        $.each( this.options.markers, function( i, data )
        {
        	// Marker

        	var defaults = 
        	{
        		lat   : false,
        		lng   : false,
        		image : '',
        		info  : ''
        	};

        	data = $.extend( {}, defaults, data );

        	var marker = new google.maps.Marker(
	        {
	        	//title    : '',
				position : new google.maps.LatLng( data.lat, data.lng ),
				icon	 : data.image,
				map      : _this.map
	        });

	        // Info Window

	        if ( data.info ) 
	        {
	        	var infowindow = new google.maps.InfoWindow(
		        {
	          		content: data.info
	        	});
	        
	        	marker.addListener( 'click', function()
	        	{
	          		infowindow.open( _this.map, marker );
	        	});
	        };

        	// Keeps marker reference

        	_this.markers.push( marker );

        	// Extends bounds with marker position

        	_this.bounds.extend( marker.position );
        });

        /* -------------------------------------------------------- */

        // Multiple markers

        if ( this.markers.length > 1 ) 
        {
        	// Makes map fit marker bounds.
        	this.map.fitBounds( this.bounds );
        }

        // 1 marker

        else if ( this.markers.length )
        {
        	this.map.setCenter( this.bounds.getCenter() );

        	// Sets zoom options
        	this.map.setZoom( this.options.zoom );
        };

        //

        jQuery( document ).trigger( 'map/init', [ this ] );
	};

	Map.defaults = 
	{
		markers  : {},
		zoom     : 8
	};

	$.fn.pbMap = function( options )
	{
		return this.each( function()
		{
			if ( $( this ).data( 'pbMap' ) ) 
			{
				return true;
			};

			var instance = new Map( this, options );

			$( this ).data( 'pbMap', instance );
		});
	};

	window.pbMap = Map;

})( jQuery );
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