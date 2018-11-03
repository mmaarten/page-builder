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

			console.log( 'init', this.options );

			this.$elem.on( 'click', '.pb-widget-add-control', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );

				// Open widget picker
				pb.widgetPicker( $widget, function( $choosen )
				{
					// Add choosen widget
					$widget.find( '> .pb-widget-inside > .pb-widget-container' ).append( $choosen );

					pb.doAction( 'widgetAdded' + $choosen.data( 'type' ), $choosen );
					pb.doAction( 'widgetAdded/type=' + $choosen.data( 'type' ), $choosen );
				});
			});

			this.$elem.on( 'click', '.pb-widget-edit-control', function( event )
			{
				// Get widget
				var $widget = $( this ).closest( '.pb-widget' );

				// Load settings
				pb.widgetSettings( $widget );
			});

			this.$elem.on( 'click', '.pb-widget-copy-control', function( event )
			{
				// Get widget
				var $widget = $( this ).closest( '.pb-widget' );

				// Create copy
				var $duplicate = pb.duplicateWidget( $widget );

				// Add copy to DOM
				$duplicate.insertAfter( $widget );
			});

			this.$elem.on( 'click', '.pb-widget-delete-control', function( event )
			{
				// Get widget
				var $widget = $( this ).closest( '.pb-widget' );

				// Remove widget
				pb.removeWidget( $widget );
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

					pb.doAction( 'widgetAdded' + $choosen.data( 'type' ), $choosen );
					pb.doAction( 'widgetAdded/type=' + $choosen.data( 'type' ), $choosen );
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

			this.addAction( 'widgetUpdated', function( $widget )
			{
				pb.loadWidgetPreview( $widget );
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

			// Notify init
			this.doAction( 'init' );

			// Load
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

		modal : function( content, options )
		{
			var defaults = 
			{
				namespace : 'pb-modal',
				closeIcon : '',
			};

			options = $.extend( {}, defaults, options );

			// Open modal
			$.featherlight( content, options );
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

			var model = $.extend( true, {}, defaults, args );

			return model;
		},

		getWidgetModel : function( widget )
		{
			var id = $( widget ).data( 'model' );

			return this.models[ id ];
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
			$widget.data( 'model', model.id );

			// Extend
			this.doAction( 'widget', $widget );
			this.doAction( 'widget/type=' + $widget.data( 'type' ), $widget );

			// Return
			return $widget;
		},

		duplicateWidget : function( widget )
		{
			var $duplicate = $( widget ).clone( true );

			// Copy models
			$duplicate.find( '.pb-widget' ).addBack().each( function()
			{
				var modelId = $( this ).data( 'model' );

				var copy = $.extend( {}, pb.models[ modelId ] );

				// Generate id
				delete copy.id;
				copy = pb.createModel( copy );

				// Register
				pb.models[ copy.id ] = copy;

				// Set reference
				$( this ).data( 'model', copy.id );
			});

			return $duplicate;
		},

		removeWidget : function( widget )
		{
			var $widget = $( widget );

			// Remove models
			$widget.find( '.pb-widget' ).addBack().each( function()
			{
				var modelId = $( this ).data( 'model' );

				delete pb.models[ modelId ];

				$.removeData( this, 'model' );
			});

			$widget.remove();

			pb.doAction( 'widgetRemoved', $widget );
			pb.doAction( 'widgetRemoved/type=' + $widget.data( 'type' ), $widget );

			return $widget;
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
					pb.modal( content, 
					{
						afterContent : function()
						{
							var modal = this;

							// Set modal id
							this.$content.closest( '.pb-modal' )
								.attr( 'id', 'pb-widget-picker-modal' );

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
					pb.modal( content, 
					{
						afterContent : function()
						{
							var modal = this;

							// Set modal id
							this.$content.closest( '.pb-modal' )
								.attr( 'id', 'pb-widget-settings-modal' );

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

										pb.doAction( 'widgetUpdated', $widget );
										pb.doAction( 'widgetUpdated/type=' + $widget.data( 'type' ), $widget );

										// Close modal
										modal.close();
									},
								});
							});

							pb.doAction( 'widgetSettings', this.$content, $widget );
							pb.doAction( 'widgetSettings/type=' + $widget.data( 'type' ), this.$content, $widget );
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
