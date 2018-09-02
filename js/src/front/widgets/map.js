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