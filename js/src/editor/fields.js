(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	pb.field = Class.extend(
	{
		id : null,

		init : function( id )
		{
			this.id = id;

			var _this = this;
			
			pb.addAction( 'field/type=' + this.id, function()
			{
				_this.field.apply( _this, arguments );
			});

			pb.addAction( 'fieldDestroy/type=' + this.id, function()
			{
				_this.destroy.apply( _this, arguments );
			});

			pb.addAction( 'fieldUpdateInput/type=' + this.id, function()
			{
				_this.updateInput.apply( _this, arguments );
			});
		},

		field : function( $field )
		{
			
		},

		updateInput : function( $field )
		{
			
		},

		destroy : function( $field )
		{
			
		}
	});

	pb.fields = 
	{
		init : function( $content )
		{
			var _this = this;

			// Match height for fields with custom width
		
			$content.find( '.pb-field[style*="width"]' ).matchHeight(
			{
				byRow : true
			});

			// Init fields

			$content.find( '.pb-field' ).each( function()
			{
				if ( $( this ).closest( '.pb-clone' ).length ) 
				{
					return true;
				};

				_this.doFieldAction( 'field', $( this ) );
			});
		},

		destroy : function( $content )
		{
			var _this = this;

			$content.find( '.pb-field' ).each( function()
			{
				_this.doFieldAction( 'fieldDestroy', $( this ) );
			});
		},

		doFieldAction : function( tag, $field )
		{
			var tags = 
			[
				tag,
				tag + '/type='  + $field.data( 'type' ),
				tag + '/key=' + $field.data( 'key' )
			];

			for ( var i in tags )
			{
				arguments[0] = tags[i];

				pb.doAction.apply( this, arguments );
			}
		}
	};

	pb.addAction( 'widgetSettings', function( $widget, $content )
	{
		pb.fields.init( $content );
	});

	pb.addAction( 'widgetSettingsClose', function( $widget, $content )
	{
		pb.fields.destroy( $content );
	});

})( jQuery );
