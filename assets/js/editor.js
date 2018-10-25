(function( $ )
{
	"use strict";

	function PB( elem, options )
	{
		this.$elem   = $( elem );
		this.options = $.extend( {}, PB.defaultOptions, options );

		this.models = {};

		var _this = this;

		// Widget Toggle Control
		this.$elem.on( 'click', '.pb-widget-toggle-control, .pb-widget-title', function( event )
		{
			var $widget = $( this ).closest( '.pb-widget' );

			$widget.toggleClass( 'closed' );
		});

		// Widget Add Control
		this.$elem.on( 'click', '.pb-widget-add-control', function( event )
		{
			var $widget = $( this ).closest( '.pb-widget' );

			_this.widgetPicker( $widget, function( $choosen )
			{
				alert('!')
				$widget.find( '> .pb-widget-inside > .pb-widget-container' )
					.append( $choosen );
			});
		});

		// Widget Edit Control
		this.$elem.on( 'click', '.pb-widget-edit-control', function( event )
		{
			var $widget = $( this ).closest( '.pb-widget' );
		});

		// Widget Copy Control
		this.$elem.on( 'click', '.pb-widget-copy-control', function( event )
		{
			var $widget = $( this ).closest( '.pb-widget' );

			var $duplicate = _this.duplicateWidget( $widget );

			$duplicate.insertAfter( $widget );
		});

		// Widget Delete Control
		this.$elem.on( 'click', '.pb-widget-delete-control', function( event )
		{
			var $widget = $( this ).closest( '.pb-widget' );

			if ( window.confirm( _this.options.confirmDelete ) ) 
			{
				_this.removeWidget( $widget );
			};
		});

		// Main Add Widget Control
		this.$elem.on( 'click', '.pb-add-widget-control', function( event )
		{
			_this.widgetPicker( null, function( $choosen )
			{
				_this.$elem.find( '.pb-widgets' ).append( $choosen );
			});
		});

		// Load Handler
		this.$elem.on( 'pb/load', function( event, response )
		{
			console.log( event.type );

			// Add widgets
			$.each( response.models, function( i, model )
			{
				console.log( model );

				var $widget = _this.createWidget( model );
				var $parent = _this.$elem.find( '.pb-widgets' );

				if ( model.parent ) 
				{
					$parent = _this.$elem.find( '.pb-widgets .pb-widget' ).filter( function()
					{
						return $( this ).data( 'model' ) == model.parent;
					}).find( '> .pb-widget-inside > .pb-widget-container' );
				}

				$parent.append( $widget );
			});
		});

		// Sorting
		this.$elem.find( '.pb-widgets' ).sortable(
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

		$( document ).trigger( 'pb/init', [ this ] );

		this.load();
	}

	PB.defaultOptions = 
	{
		post           : null,
		nonceName      : null,
		nonce          : null,
		widgetDefaults : {},
		confirmDelete  : 'Are you sure you want to delete this widget?',
		ajaxurl        : window.ajaxurl || null,
	};

	PB.generateId = function()
	{
		function s4() 
		{
    		return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
  		}

  		return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
	}

	PB.prototype.prepareAjax = function( data ) 
	{
		data = $.extend( {}, data );

		if ( this.options.nonceName ) 
		{
			data[ this.options.nonceName ] = this.options.nonce;
		}

		return data;
	};

	PB.prototype.createModel = function( model ) 
	{
		var defaultData = {};

		if ( typeof model === 'object' && typeof model.type !== 'undefined' ) 
		{
			if ( typeof this.options.widgetDefaults[ model.type ] !== 'undefined' ) 
			{
				defaultData = $.extend( {}, this.options.widgetDefaults[ model.type ] );
			}
		}

		var defaults = 
		{
			id   : PB.generateId(),
			type : null,
			data : defaultData,
		};

		model = $.extend( {}, defaults, model );

		return model;
	};

	PB.prototype.createWidget = function( model ) 
	{
		var model = this.createModel( model );

		this.models[ model.id ] = model;

		var $widget = this.$elem.find( '.pb-available-widgets .pb-widget' ).filter( function()
		{
			return $( this ).data( 'type' ) == model.type;
		});

		$widget.data( 'model', model.id );

		return $widget;
	};

	PB.prototype.removeWidget = function( widget ) 
	{
		var $widget = $( widget ), _this = this;

		// Remove models
		$widget.find( '.pb-widget' ).addBack().each( function()
		{
			var modelId = $( this ).data( 'model' );

			delete _this.models[ modelId ];

			$.removeData( this, 'model' );
		});

		// Remove widgets
		return $widget.remove();
	};

	PB.prototype.duplicateWidget = function( widget ) 
	{
		var $duplicate = $( widget ).clone( true ), _this = this;

		// Duplicate models
		$duplicate.find( '.pb-widget' ).addBack().each( function()
		{
			var modelId = $( this ).data( 'model' );

			var copy = $.extend( {}, _this.models[ modelId ] );

			delete copy.id;

			copy = _this.createModel( copy );

			_this.models[ copy.id ] = copy;

			$( this ).data( 'model', copy.id );
		});

		return $duplicate;
	};

	PB.prototype.widgetPicker = function( parentWidget, callback ) 
	{
		var _this = this;

		// Load content
		$.ajax( 
		{
			url : this.options.ajaxurl,
			data : this.prepareAjax( 
			{
				action : 'pb_widget_picker',
			}),
			method : 'post',
			context : this,
			success : function( content )
			{
				// Open modal

				$.featherlight( content, 
				{
					namespace : 'pb-modal',
					closeIcon : '',
					afterContent : function()
					{
						this.$content.closest( '.pb-modal' )
							.attr( 'id', 'pb-widget-picker-modal' );

						var modal = this;

						this.$content.on( 'click', '.pb-widget-button', function( event )
						{
							var $choosen = _this.createWidget( 
							{
								type : $( this ).data( 'type' ),
							});

							console.log( $choosen.get(0) )

							callback( $choosen );

							modal.close();
						});
					},
				});
			}
		});
	};

	PB.prototype.load = function() 
	{
		this.models = {};
		this.$elem.find( '.pb-widgets' ).empty();

		$.ajax( 
		{
			url : this.options.ajaxurl,
			data : this.prepareAjax( 
			{
				action : 'pb_load',
				post   : this.options.post,
			}),
			method : 'post',
			context : this,
			success : function( response )
			{
				this.$elem.trigger( 'pb/load', [ response ] );
			}
		});
	};

	$.fn.pageBuilder = function( options )
	{
		return this.each( function()
		{
			if ( typeof $( this ).data( 'pageBuilder' ) === 'undefined' ) 
			{
				var instance = new PB( this, options );

				$( this ).data( 'pageBuilder', instance );
			}
		});
	}

})( jQuery );