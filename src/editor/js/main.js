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
			chunkLength : 10,
			confirmDelete : 'Are you sure you want to delete?',
		},

		init : function( elem, options )
		{
			this.$elem   = $( elem );
			this.options = $.extend( {}, this.defaultOptions, options );

			console.log( 'init', this.options );

			// Widget add button click
			this.$elem.on( 'click', '.pb-widget-add-control', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );

				// Open widget picker
				pb.widgetPicker( function( $choosen )
				{
					// Parent row
					if ( $widget.data( 'type' ) == 'row' ) 
					{
						// Other than column
						if ( $choosen.data( 'type' ) != 'column' ) 
						{
							alert( 'Only columns allowed.' );

							return;
						}

						// Column
						pb.addWidget( $choosen, $widget );
						pb.loadWidgetPreview( $choosen );

						return;
					}

					// Parent column
					if ( $widget.data( 'type' ) == 'column' )
					{
						// Column
						if ( $choosen.data( 'type' ) == 'column' ) 
						{
							alert( 'No columns allowed.' );

							return;
						}

						// Row
						if ( $choosen.data( 'type' ) == 'row' ) 
						{
							// Add column
							var $column = pb.createWidget( 'column' );

							pb.addWidget( $choosen, $widget );
							pb.addWidget( $column, $choosen );

							pb.loadWidgetPreview( $choosen );

							return;
						}

						// Other than row or column

						pb.addWidget( $choosen, $widget );
						pb.loadWidgetPreview( $choosen );

						return;
					}

					alert( 'Not allowed.' );
					
				}, $widget );
			});

			// Widget edit button click
			this.$elem.on( 'click', '.pb-widget-edit-control', function( event )
			{
				// Get widget
				var $widget = $( this ).closest( '.pb-widget' );

				// Load settings
				pb.widgetSettings( $widget );
			});

			// Widget copy button click
			this.$elem.on( 'click', '.pb-widget-copy-control', function( event )
			{
				// Get widget
				var $widget = $( this ).closest( '.pb-widget' );

				// Create copy
				var $duplicate = pb.duplicateWidget( $widget );

				// Add copy to DOM
				pb.addWidget( $duplicate, $widget, 'insertAfter' );

				// Load preview
				pb.loadWidgetPreview( $duplicate );
			});

			// Widget delete button click
			this.$elem.on( 'click', '.pb-widget-delete-control', function( event )
			{
				if ( ! window.confirm( pb.options.confirmDelete ) ) 
				{
					return;
				}

				// Get widget
				var $widget = $( this ).closest( '.pb-widget' );

				// Remove widget
				pb.removeWidget( $widget );
			});

			// Widget toggle button click
			this.$elem.on( 'click', '.pb-widget-toggle-control, .pb-widget-title', function( event )
			{
				// Get widget
				var $widget = $( this ).closest( '.pb-widget' );

				// Open or close
				$widget.toggleClass( 'closed' );
			});

			// Add Widget button click
			this.$elem.on( 'click', '.pb-add-widget-control', function( event )
			{
				// Open widget picker
				pb.widgetPicker( function( $choosen )
				{
					// Row
					if ( $choosen.data( 'type' ) == 'row' ) 
					{
						// Add column
						var $column = pb.createWidget( 'column' );

						pb.addWidget( $choosen );
						pb.addWidget( $column, $choosen );

						pb.loadWidgetPreview( $choosen );
					}

					// Column
					else if ( $choosen.data( 'type' ) == 'column' ) 
					{
						// Add inside row
						var $row = pb.createWidget( 'row' );

						pb.addWidget( $row );
						pb.addWidget( $choosen, $row );

						pb.loadWidgetPreview( $row );
					}

					// No row or column
					else
					{
						// Put inside row->column
						var $row    = pb.createWidget( 'row' );
						var $column = pb.createWidget( 'column' );

						pb.addWidget( $row );
						pb.addWidget( $column, $row );
						pb.addWidget( $choosen, $column );

						pb.loadWidgetPreview( $row );
					}
				});
			});

			// Sort start
			this.$elem.on( 'sortstart', '.ui-sortable', function( event, ui )
			{
				if ( ui.item.is( '.pb-widget' ) ) 
				{
					pb.doAction( 'widgetSortStart'                               , ui.item );
					pb.doAction( 'widgetSortStart/type=' + ui.item.data( 'type' ), ui.item );
				}
			});

			// Sort stop
			this.$elem.on( 'sortstop', '.ui-sortable' , function( event, ui )
			{
				if ( ui.item.is( '.pb-widget' ) ) 
				{
					pb.doAction( 'widgetSortStop'                               , ui.item );
					pb.doAction( 'widgetSortStop/type=' + ui.item.data( 'type' ), ui.item );
				}
			});

			// post edit form submit
			
			var submit = false;

			this.$elem.closest( 'form' ).on( 'submit', function( event )
			{
				if ( ! submit ) 
				{
					pb.save();

					submit = true;
				}
			});

			this.addAction( 'load', function( response )
			{
				$.each( response.models, function( i, model )
				{
					var $widget = pb.createWidget( model );
					var $parentWidget = undefined;

					if ( model.parent ) 
					{
						$parentWidget = pb.$elem.find( '.pb-widgets .pb-widget' ).filter( function()
						{
							return $( this ).data( 'model' ) == model.parent;
						});
					};

					pb.addWidget( $widget, $parentWidget );
				});

				pb.loadWidgetPreview();
			});

			this.addAction( 'widgetSortStart', function( $widget )
			{
				if ( $widget.hasClass( 'closed' ) ) 
				{
					$widget.addClass( 'no-close' );
				}

				else
				{
					$widget.addClass( 'closed' );
				}
			});

			this.addAction( 'widgetSortStop', function( $widget )
			{
				if ( ! $widget.hasClass( 'no-close' ) ) 
				{
					$widget.removeClass( 'closed' );
				}

				else
				{
					$widget.removeClass( 'no-close' );
				}
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

		chunk : function( object, amount )
		{
			var values = Object.values(object);
			var final = [];
			var counter = 0;
			var portion = {};

			for ( var key in object ) 
			{
				if ( counter !== 0 && counter % amount === 0 ) 
				{
					final.push(portion);

					portion = {};
				}

				portion[ key ] = values[ counter ];

				counter++
			}

			final.push( portion );

			return final;
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

		removeAction : function()
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.removeAction.apply( this, arguments );
		},

		doAction : function()
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

		removeFilter : function()
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.removeFilter.apply( this, arguments );
		},

		applyFilters : function()
		{
			// Prefix tag
			arguments[0] = 'pb/' + arguments[0];

			return wp.hooks.applyFilters.apply( this, arguments );
		},

		createModel : function( args )
		{
			// Args is model type
			if ( typeof args !== 'object' ) 
			{
				args = { type : args };
			};

			// Defaults

			var defaultData = {};

			if ( args.type !== undefined && this.options.widgetDefaults[ args.type ] !== undefined )
			{
				defaultData = $.extend( {}, this.options.widgetDefaults[ args.type ] );
			};

			var defaults = 
			{
				id : this.generateId(),
				type : null,
				data : defaultData,
			};

			// Create model
			var model = $.extend( true, {}, defaults, args );

			// Return
			return model;
		},

		getWidgetModel : function( widget )
		{
			var modelId = $( widget ).data( 'model' );
			var model = this.models[ modelId ];

			return $.extend( {}, model );
		},

		createWidget : function( args )
		{
			// Create model
			var model = this.createModel( args );

			// Register model
			this.models[ model.id ] = model;

			// Create widget
			var $widget = this.$elem.find( '.pb-available-widgets .pb-widget' ).filter( function()
			{
				return $( this ).data( 'type' ) == model.type;
			}).clone();

			// Set model reference
			$widget.data( 'model', model.id );

			// Notify
			this.doAction( 'widget', $widget );
			this.doAction( 'widget/type=' + $widget.data( 'type' ), $widget );

			// Return
			return $widget;
		},

		addWidget : function( widgetToAdd, widget, context )
		{
			var $widgetToAdd = $( widgetToAdd );

			// Get parent

			var $parent = this.$elem.find( '.pb-widgets' );

			if ( widget !== undefined ) 
			{
				$parent = $( widget ).find( '> .pb-widget-inside > .pb-widget-container' );
			};

			// Add widget

			switch( context )
			{
				case 'prepend' :
					$parent.prepend( $widgetToAdd );
					break;

				case 'insertBefore' :
					$widgetToAdd.insertBefore( widget );
					break;

				case 'insertAfter' :
					$widgetToAdd.insertAfter( widget );
					break;

				default :
					$parent.append( $widgetToAdd );
			}

			// Loop widget and child widgets
			$( widgetToAdd ).find( '.pb-widget' ).addBack().each( function()
			{
				var $widget = $( this );

				// Notify
				pb.doAction( 'widgetAdded'                               , $widget );
				pb.doAction( 'widgetAdded/type=' + $widget.data( 'type' ), $widget );
			});
		},

		duplicateWidget : function( widget )
		{
			// Copy model
			var modelCopy = this.getWidgetModel( widget );

			delete modelCopy.id;

			// Copy Widget
			var $widgetCopy = this.createWidget( modelCopy );

			// Copy Children
			$( widget ).find( '> .pb-widget-inside > .pb-widget-container > .pb-widget' ).each( function()
			{
				var $child = pb.duplicateWidget( this );

				$widgetCopy.find( '> .pb-widget-inside > .pb-widget-container' ).append( $child );
			});

			// Return
			return $widgetCopy;
		},

		removeWidget : function( widget )
		{
			var $widgets = $( widget ).find( '.pb-widget' ).addBack();

			// Loop widget and child widgets
			$widgets.each( function()
			{
				// Remove model
				var model = pb.getWidgetModel( this );

				delete pb.models[ model.id ];

				// Remove model reference 
				$.removeData( this, 'model' );
			});

			// Remove widget
			$( widget ).remove();

			// Notify
			$widgets.each( function()
			{
				pb.doAction( 'widgetRemoved'                                 , $( this ) );
				pb.doAction( 'widgetRemoved/type=' + $( this ).data( 'type' ), $( this ) );
			});

			// Return
			return $( widget );
		},

		updateWidget : function( widget, k, v )
		{
			// Get new data

			var data;

			if ( typeof k === 'object' ) 
			{
				data = k;
			}

			else
			{
				data = { k : v };
			}

			// Update model

			var model = this.getWidgetModel( widget );

			$.extend( true, model.data, data );

			this.models[ model.id ] = model;

			// Notify

			pb.doAction( 'widgetUpdated'                                   , $( widget ) );
			pb.doAction( 'widgetUpdated/type=' + $( widget ).data( 'type' ), $( widget ) );
		},

		widgetPicker : function( callback, parentWidget )
		{
			// Load content
			$.ajax(
			{
				url : this.options.ajaxurl,
				data : this.prepareAjax(
				{
					action : 'pb_widget_picker',
					parent : $( parentWidget ).data( 'type' ),
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
			var model = this.getWidgetModel( widget );

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

								console.log( 'Sanitize user input' );

								// Sanitize user input
								$.ajax(
								{
									url : pb.options.ajaxurl,
									data : $( this ).serialize(),
									method : 'post',
									success : function( options )
									{
										// Update widget
										pb.updateWidget( widget, options );

										// Close modal
										modal.close();
									},
								});
							});

							pb.doAction( 'widgetSettings', this.$content, $( widget ) );
							pb.doAction( 'widgetSettings/type=' + $( widget ).data( 'type' ), this.$content, $( widget ) );
						},
					});
				},
			});
		},

		loadWidgetPreview : function( widget )
		{
			/**
			 * Get widgets
			 * -------------------------------------------------------
			 */

			var $widgets;

			// Supplied widget and children
			if ( widget !== undefined ) 
			{
				$widgets = $( widget ).find( '.pb-widget' ).addBack();
			}

			// All widgets
			else
			{
				$widgets = this.$elem.find( '.pb-widgets .pb-widget' );
			}

			/**
			 * Get models
			 * -------------------------------------------------------
			 */

			var models = {};

			// Loop widgets
			$widgets.each( function()
			{
				// Get model
				var model = pb.getWidgetModel( this );

				models[ model.id ] = model;
			});

			/**
			 * Check if loading is needed
			 * -------------------------------------------------------
			 */

			if ( ! Object.keys( models ).length ) 
			{
				return;
			};

			/**
			 * Load
			 * -------------------------------------------------------
			 */

			// Split models into chunks
			var chunks = this.chunk( models, this.options.chunkLength );

			// Loop chunks
			chunks.forEach( function( models, index )
			{
				// Load
				$.ajax(
				{
					url : ajaxurl,
					method : 'POST',
					async : false,
					data : pb.prepareAjax(
					{
						action : 'pb_widget_preview',
						models : models,
					}),
					success : function( preview )
					{
						// Set widget preview content
						$.each( preview, function( modelId, content )
						{
							// Get widget
							var $widget = $widgets.filter( function()
							{
								return $( this ).data( 'model' ) == modelId;
							});

							// Set content
							$widget.find( '> .pb-widget-inside > .pb-widget-preview' )
								.html( content );
						});
					}
				});
			});
		},

		load : function()
		{
			// Reset

			this.models = {};
			this.$elem.find( '.pb-widgets' ).empty();

			// Load

			this.$elem.addClass( 'loading' );

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
					pb.doAction( 'load', response );
				},
				complete : function()
				{
					pb.$elem.removeClass( 'loading' );
				}
			});
		},

		save : function()
		{
			/**
			 * Get models from widgets
			 * -------------------------------------------------------
			 */

			var models = {};

			this.$elem.find( '.pb-widgets .pb-widget' ).each( function()
			{
				var model = pb.getWidgetModel( this );

				models[ model.id ] = $.extend( model, 
				{
					index  : $( this ).index(),
					parent : $( this ).parent().closest( '.pb-widget' ).data( 'model' ) || '',
				});
			});

			// Update models

			this.models = models;

			/**
			 * Save models
			 * -------------------------------------------------------
			 */

			// Split models into chunks

			var chunks = this.chunk( this.models, this.options.chunkLength );

			chunks.forEach( function( models, index )
			{
				var first = index == 0;

				$.ajax(
				{
					url : ajaxurl,
					method : 'POST',
					async : false,
					data : pb.prepareAjax(
					{
						action : 'pb_save',
						post   : pb.options.post,
						models : models,
						append : first ? 0 : 1,
					}),
				});
			});
		},
	};

	window.pb = pb;

})( jQuery, window, undefined );
