(function( $, window, undefined )
{
	"use strict";

	var pb = 
	{
		$elem : null,
		options : {},
		models : {},
		defaultOptions : 
		{
			post : null,
			nonceName : null,
			nonce : null,
			widgetDefaults : {},
			ajaxurl : window.ajaxurl || null,
		},

		init : function( elem, options )
		{
			this.$elem   = $( elem );
			this.options = $.extend( {}, this.defaultOptions, options );

			console.log( this.options );

			this.$elem.on( 'click', '.pb-widget-add-control', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );

				// Open widget picker
				pb.widgetPicker( $widget, function( $choosen )
				{
					// Add choosen widget
					$widget.find( '> .pb-widget-inside > .pb-widget-container' ).append( $choosen );
				});
			});

			this.$elem.on( 'click', '.pb-widget-edit-control', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );

				pb.widgetSettings( $widget );
			});

			this.$elem.on( 'click', '.pb-widget-copy-control', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );
			});

			this.$elem.on( 'click', '.pb-widget-delete-control', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );
			});

			this.$elem.on( 'click', '.pb-widget-toggle-control, .pb-widget-title', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );

				$widget.toggleClass( 'closed' );
			});

			this.$elem.on( 'click', '.pb-add-widget-control', function( event )
			{
				// Open widget picker
				pb.widgetPicker( null, function( $choosen )
				{
					// Add choosen widget
					pb.$elem.find( '.pb-widgets' ).append( $choosen );
				});
			});

			this.$elem.closest( 'form' ).on( 'submit', function( event )
			{
				pb.save();
			});

			this.addAction( 'load', function( response )
			{
				$.each( response.models, function( i, model )
				{
					var $widget = pb.createWidget( model );
					var $parent = pb.$elem.find( '.pb-widgets' );

					if ( model.parent ) 
					{
						$parent = pb.$elem.find( '.pb-widgets .pb-widget' ).filter( function()
						{
							return $( this ).data( 'model' ) == model.parent;
						}).find( '> .pb-widget-inside > .pb-widget-container' );
					};

					$parent.append( $widget );
				});

				pb.loadWidgetPreview();
			});

			// Widget Sorting
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

			this.doAction( 'init' );

			this.load();
		},

		generateId : function()
		{
			function s4() 
			{
    			return Math.floor( ( 1 + Math.random() ) * 0x10000 ).toString( 16 ).substring( 1 );
  			}
  			
  			return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
		},

		prepareAjax : function( data )
		{
			data = $.extend( {}, data );

			if ( this.options.nonceName ) 
			{
				data[ this.options.nonceName ] = this.options.nonce;
			};

			return data;
		},

		addAction : function()
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.addAction.apply( this, arguments );
		},

		removeAction : function( model )
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.removeAction.apply( this, arguments );
		},

		doAction : function( model )
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.doAction.apply( this, arguments );
		},

		addFilter : function()
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.addFilter.apply( this, arguments );
		},

		removeFilter : function( model )
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.removeFilter.apply( this, arguments );
		},

		applyFilters : function( model )
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			return wp.hooks.applyFilters.apply( this, arguments );
		},

		createModel : function( args )
		{
			var defaultData = {};

			if ( args.type !== undefined && this.options.widgetDefaults[ args.type ] !== undefined )
			{
				defaultData = $.extend( {}, this.options.widgetDefaults[ args.type ] );
			}

			var defaults = 
			{
				id : this.generateId(),
				type : null,
				data : defaultData,
			};

			var model = $.extend( {}, defaults, args );

			return model;
		},

		createWidget : function( model )
		{
			// Make sure model is setup correctly
			model = this.createModel( model );

			// Register model
			this.models[ model.id ] = model;

			// Create widget
			var $widget = this.$elem.find( '.pb-available-widgets .pb-widget' ).filter( function()
			{
				return $( this ).data( 'type' ) == model.type;
			}).clone();

			// Set model reference
			$widget.attr( 'data-model', model.id );

			// Return
			return $widget;
		},

		duplicateWidget : function( model )
		{

		},

		removeWidget : function( model )
		{

		},

		widgetPicker : function( parentWidget, callback )
		{
			// Load content
			$.ajax(
			{
				url : this.options.ajaxurl,
				data : this.prepareAjax(
				{
					action : 'pb_widget_picker',
					parent : $( parentWidget ).data( 'type' ) || '',
				}),
				method : 'post',
				success : function( content )
				{
					// Open modal
					$.featherlight( content, 
					{
						namespace : 'pb-modal',
						closeIcon : '',
						afterContent : function()
						{
							var modal = this;

							// Widget click
							this.$content.on( 'click', '.pb-widget', function()
							{
								// Create widget
								var $choosen = pb.createWidget(
								{
									type : $( this ).data( 'type' ),
								});

								// Callback
								callback( $choosen );

								// Close modal
								modal.close();
							});
						},
					});
				},
			});
		},

		widgetSettings : function( widget )
		{
			var $widget = $( widget );
			var model = this.models[ $widget.data( 'model' ) ];

			// Load content
			$.ajax(
			{
				url : this.options.ajaxurl,
				data : this.prepareAjax(
				{
					action : 'pb_widget_settings',
					model : model,
				}),
				method : 'post',
				success : function( content )
				{
					// Open modal
					$.featherlight( content, 
					{
						namespace : 'pb-modal',
						closeIcon : '',
						afterContent : function()
						{
							var modal = this;

							// Form submit
							this.$content.on( 'submit', 'form', function( event )
							{
								event.preventDefault();

								// Sanitize user input
								$.ajax(
								{
									url : pb.options.ajaxurl,
									data : $( this ).serialize(),
									method : 'post',
									success : function( options )
									{
										// Update model

										var updated = $.extend( {}, model );

										updated.data = options;

										pb.models[ model.id ] = updated;

										// Close modal
										modal.close();
									},
								});
							});

							pb.doAction( 'widgetSettings', this.$content, $widget );
						},
					});
				},
			});
		},

		loadWidgetPreview : function( widget )
		{
			// Get widgets

			var $widgets = this.$elem.find( '.pb-widgets .pb-widget' );

			if ( widget !== undefined ) 
			{
				$widgets = $( widget ).find( '.pb-widget' ).addBack();
			}

			// Get models

			var models = {};

			$widgets.each( function()
			{
				var model = pb.models[ $( this ).data( 'model' ) ];

				models[ model.id ] = $.extend( {}, model );
			});

			// Check if models

			if ( ! Object.keys( models ).length ) 
			{
				return;
			};

			// Load

			$.ajax(
			{
				url : this.options.ajaxurl,
				data : this.prepareAjax(
				{
					action : 'pb_widget_preview',
					models : models,
				}),
				method : 'post',
				success : function( preview )
				{
					console.log( 'preview response', preview );

					// Add preview content
					$.each( preview, function( modelId, content )
					{
						var $widget = $widgets.filter( function()
						{
							return $( this ).data( 'model' ) == modelId;
						});

						$widget.find( '> .pb-widget-inside > .pb-widget-preview' )
							.html( content );
					});
				}
			});
		},

		load : function()
		{
			console.log( 'load' );

			// Reset

			this.models = {};
			this.$elem.find( '.pb-widgets' ).empty();

			// Load

			$.ajax(
			{
				url : this.options.ajaxurl,
				data : this.prepareAjax(
				{
					action : 'pb_load',
					post : this.options.post,
				}),
				method : 'post',
				success : function( response )
				{
					console.log( 'load response', response );

					pb.doAction( 'load', response );
				}
			});
		},

		save : function()
		{
			console.log( 'save' );

			// Get models from widgets

			var models = {};

			this.$elem.find( '.pb-widgets .pb-widget' ).each( function()
			{
				var modelId = $( this ).data( 'model' );
				var model = pb.models[ modelId ];

				models[ model.id ] = $.extend( {}, model, 
				{
					index : $( this ).index(),
					parent : $( this ).parent().closest( '.pb-widget' ).data( 'model' ) || '',
				});
			});

			// Update models

			this.models = models;

			// Save

			$.ajax(
			{
				url : this.options.ajaxurl,
				data : this.prepareAjax(
				{
					action : 'pb_save',
					post : this.options.post,
					models : this.models,
				}),
				method : 'post',
				success : function( response )
				{
					console.log( 'save response', response );
				}
			});
		},
	};

	window.pb = pb;

})( jQuery, window, undefined );


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
				var $active = $nav.find( '.nav-tab-active' );

				if ( $active.length ) 
				{
					$active.removeClass( 'nav-tab-active' )
						.data( 'field' ).nextUntil( '[data-type="tab"]' )
							.hide();
				};

				var $tab = $nav.find( '.nav-tab' ).eq( index );

				$tab.addClass( 'nav-tab-active' )
					.data( 'field' ).nextUntil( '[data-type="tab"]' )
						.show();
			};

			var $nav = $( '<h2 class="nav-tab-wrapper"></h2>' );

			$tabFields.each( function()
			{
				var $field = $( this );
				var $tab   = $( '<a href="#" class="nav-tab"></a>' );

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

			$nav.insertBefore( $tabFields.first() );

			setActiveTab( 0 );
		});
	});

})( jQuery );