( function( window, undefined ) {
	'use strict';

	/**
	 * Handles managing all events for whatever you plug it into. Priorities for hooks are based on lowest to highest in
	 * that, lowest priority hooks are fired first.
	 */
	var EventManager = function() {
		var slice = Array.prototype.slice;
		
		/**
		 * Maintain a reference to the object scope so our public methods never get confusing.
		 */
		var MethodsAvailable = {
			removeFilter : removeFilter,
			applyFilters : applyFilters,
			addFilter : addFilter,
			removeAction : removeAction,
			doAction : doAction,
			addAction : addAction
		};

		/**
		 * Contains the hooks that get registered with this EventManager. The array for storage utilizes a "flat"
		 * object literal such that looking up the hook utilizes the native object literal hash.
		 */
		var STORAGE = {
			actions : {},
			filters : {}
		};

		/**
		 * Adds an action to the event manager.
		 *
		 * @param action Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param [priority=10] Used to control when the function is executed in relation to other callbacks bound to the same hook
		 * @param [context] Supply a value to be used for this
		 */
		function addAction( action, callback, priority, context ) {
			if( typeof action === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'actions', action, callback, priority, context );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs an action if it exists. You can pass as many arguments as you want to this function; the only rule is
		 * that the first argument must always be the action.
		 */
		function doAction( /* action, arg1, arg2, ... */ ) {
			var args = slice.call( arguments );
			var action = args.shift();

			if( typeof action === 'string' ) {
				_runHook( 'actions', action, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified action if it contains a namespace.identifier & exists.
		 *
		 * @param action The action to remove
		 * @param [callback] Callback function to remove
		 */
		function removeAction( action, callback ) {
			if( typeof action === 'string' ) {
				_removeHook( 'actions', action, callback );
			}

			return MethodsAvailable;
		}

		/**
		 * Adds a filter to the event manager.
		 *
		 * @param filter Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param [priority=10] Used to control when the function is executed in relation to other callbacks bound to the same hook
		 * @param [context] Supply a value to be used for this
		 */
		function addFilter( filter, callback, priority, context ) {
			if( typeof filter === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'filters', filter, callback, priority, context );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs a filter if it exists. You should only ever pass 1 argument to be filtered. The only rule is that
		 * the first argument must always be the filter.
		 */
		function applyFilters( /* filter, filtered arg, arg2, ... */ ) {
			var args = slice.call( arguments );
			var filter = args.shift();

			if( typeof filter === 'string' ) {
				return _runHook( 'filters', filter, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified filter if it contains a namespace.identifier & exists.
		 *
		 * @param filter The action to remove
		 * @param [callback] Callback function to remove
		 */
		function removeFilter( filter, callback ) {
			if( typeof filter === 'string') {
				_removeHook( 'filters', filter, callback );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified hook by resetting the value of it.
		 *
		 * @param type Type of hook, either 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to remove
		 * @private
		 */
		function _removeHook( type, hook, callback, context ) {
			var handlers, handler, i;
			
			if ( !STORAGE[ type ][ hook ] ) {
				return;
			}
			if ( !callback ) {
				STORAGE[ type ][ hook ] = [];
			} else {
				handlers = STORAGE[ type ][ hook ];
				if ( !context ) {
					for ( i = handlers.length; i--; ) {
						if ( handlers[i].callback === callback ) {
							handlers.splice( i, 1 );
						}
					}
				}
				else {
					for ( i = handlers.length; i--; ) {
						handler = handlers[i];
						if ( handler.callback === callback && handler.context === context) {
							handlers.splice( i, 1 );
						}
					}
				}
			}
		}

		/**
		 * Adds the hook to the appropriate storage container
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to add to our event manager
		 * @param callback The function that will be called when the hook is executed.
		 * @param priority The priority of this hook. Must be an integer.
		 * @param [context] A value to be used for this
		 * @private
		 */
		function _addHook( type, hook, callback, priority, context ) {
			var hookObject = {
				callback : callback,
				priority : priority,
				context : context
			};

			// Utilize 'prop itself' : http://jsperf.com/hasownproperty-vs-in-vs-undefined/19
			var hooks = STORAGE[ type ][ hook ];
			if( hooks ) {
				hooks.push( hookObject );
				hooks = _hookInsertSort( hooks );
			}
			else {
				hooks = [ hookObject ];
			}

			STORAGE[ type ][ hook ] = hooks;
		}

		/**
		 * Use an insert sort for keeping our hooks organized based on priority. This function is ridiculously faster
		 * than bubble sort, etc: http://jsperf.com/javascript-sort
		 *
		 * @param hooks The custom array containing all of the appropriate hooks to perform an insert sort on.
		 * @private
		 */
		function _hookInsertSort( hooks ) {
			var tmpHook, j, prevHook;
			for( var i = 1, len = hooks.length; i < len; i++ ) {
				tmpHook = hooks[ i ];
				j = i;
				while( ( prevHook = hooks[ j - 1 ] ) &&  prevHook.priority > tmpHook.priority ) {
					hooks[ j ] = hooks[ j - 1 ];
					--j;
				}
				hooks[ j ] = tmpHook;
			}

			return hooks;
		}

		/**
		 * Runs the specified hook. If it is an action, the value is not modified but if it is a filter, it is.
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook ( namespace.identifier ) to be ran.
		 * @param args Arguments to pass to the action/filter. If it's a filter, args is actually a single parameter.
		 * @private
		 */
		function _runHook( type, hook, args ) {
			var handlers = STORAGE[ type ][ hook ], i, len;
			
			if ( !handlers ) {
				return (type === 'filters') ? args[0] : false;
			}

			len = handlers.length;
			if ( type === 'filters' ) {
				for ( i = 0; i < len; i++ ) {
					args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			} else {
				for ( i = 0; i < len; i++ ) {
					handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			}

			return ( type === 'filters' ) ? args[ 0 ] : true;
		}

		// return all of the publicly available methods
		return MethodsAvailable;

	};
	
	window.wp = window.wp || {};
	window.wp.hooks = window.wp.hooks || new EventManager();

} )( window );

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
			});

			// Widget delete button click
			this.$elem.on( 'click', '.pb-widget-delete-control', function( event )
			{
				// Get widget
				var $widget = $( this ).closest( '.pb-widget' );

				// Remove widget
				pb.removeWidget( $widget );
			});

			// Widget toggle button click
			this.$elem.on( 'click', '.pb-widget-toggle-control, .pb-widget-title', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );

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

			return $.extend( {}, this.models[ modelId ] );
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
				pb.doAction( 'widgetAdded' + $widget.data( 'type' )      , $widget );
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
			// Remove models
			$( widget ).find( '.pb-widget' ).addBack().each( function()
			{
				var model = pb.getWidgetModel( this );

				delete pb.models[ model.id ];

				$.removeData( this, 'model' );
			});

			// Remove
			$( widget ).remove();

			// Notify
			pb.doAction( 'widgetRemoved'                                   , $( widget ) );
			pb.doAction( 'widgetRemoved/type=' + $( widget ).data( 'type' ), $( widget ) );

			// Return
			return $widget;
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

				models[ model.id ] = $.extend( {}, model );
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

			/**
			 * Get models from widgets
			 * -------------------------------------------------------
			 */

			var models = {};

			this.$elem.find( '.pb-widgets .pb-widget' ).each( function()
			{
				var model = pb.getWidgetModel( this );

				models[ model.id ] = $.extend( {}, model, 
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

/**
 * Fields
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	pb.fields = {};
	
	pb.field = 
	{
		id : null,

		extend : function( options )
		{
			$.extend( this, options );

			pb.addAction( 'field/type=' + this.id, this.field );
		},

		field : function( $field )
		{

		},
	};

	var models = {};

	function init()
	{
		pb.addAction( 'load', load );
		pb.addAction( 'widgetSettings', initFields );
	}

	pb.addAction( 'init', init );

	function load( response )
	{
		models = response.fields;
	}

	function initFields( $content )
	{
		$content.find( '.pb-field' ).each( function()
		{
			var $field = $( this );

			pb.doAction( 'field'                              , $field );
			pb.doAction( 'field/type=' + $field.data( 'type' ), $field );
		});
	}

	function getFieldModel( $field )
	{
		var key = $field.data( 'key' );

		return models[ key ];
	}

	pb.getFieldModel = getFieldModel;

})( jQuery, window, undefined );

/**
 * Editor Field
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};
	
	var editor = pb.field.extend(
	{
		id : 'editor',

		field : function( $field )
		{
			var model = pb.getFieldModel( $field );

			if ( typeof tinymce === 'undefined' )
			{
				return false;
			};

			if ( typeof tinyMCEPreInit === 'undefined' )
			{
				return false;
			};

			var defaults = tinyMCEPreInit.mceInit.pb_content;

			// makes sure we have a valid id (no dashes)

			var $textarea = $field.find( 'textarea' );

			var newId = $textarea.attr( 'id' ).replace( /-/g, '' );

			$textarea.attr( 'id', newId );

			// removes instance if exists

			var instance = tinymce.get( newId );

			if ( instance ) 
			{
				tinymce.execCommand( 'mceRemoveControl', true, newId );

				instance.destroy();
			};

			// mce init

			var init = $.extend( {}, defaults, model.tinymce );

			init.id = newId;
			init.selector = '#' + newId;

			// Stores settings
			tinyMCEPreInit.mceInit[ newId ] = init;

			tinymce.init( init );
		},
	});

	pb.fields.editor = editor;

})( jQuery, window, undefined );

/**
 * Image Field
 *
 * @link https://codex.wordpress.org/Javascript_Reference/wp.media
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};
	
	var image = pb.field.extend(
	{
		id : 'image',

		field : function( $field )
		{
			var model = pb.getFieldModel( $field );

			console.log( model );

			// Media Picker

			var frame = null,
			$picker        = $field.find( '> .pb-media-picker' ),
			$input         = $picker.find( '.pb-media-picker-input' ),
			$image         = $picker.find( '.pb-media-picker-image' ),
			$addControl    = $picker.find( '.pb-media-picker-add' ),
			$removeControl = $picker.find( '.pb-media-picker-remove' );

			if ( $input.val() ) 
			{
				$picker.addClass( 'pb-has-value' );
			}

			else
			{
				$picker.removeClass( 'pb-has-value' );
			};

			// Add button click
			$addControl.on( 'click', function( event )
			{
				// If the media frame already exists, reopen it.
			    if ( frame ) 
			    {
					frame.open();
			      	
			      	return;
			    };

			    // Create a new media frame
			    frame = wp.media(
			    {
					title    : 'Choose Image',
					button   : { text: 'Insert Image' },
					library  : { type: [ 'image' ] },
					multiple : false
			    })

			    // Image selected
			    .on( 'select', function( event )
			    {
					var attachment = frame.state().get('selection').first().toJSON();

					// Get size data

					var size = attachment.sizes.full;

					if ( attachment.sizes.thumbnail !== undefined ) 
					{
						size = attachment.sizes.thumbnail;
					}

					// Update view

					$input.val( attachment.id );
					$image.attr( 'src', size.url );

					$picker.addClass( 'pb-has-value' );
			    });

			});

			// Remove button click
			$removeControl.on( 'click', function( event )
			{
				$input.val( '' );
				$image.removeAttr( 'src' );

				$picker.removeClass( 'pb-has-value' );
			});
		},
	});

	pb.fields.image = image;

})( jQuery, window, undefined );

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
/**
 * Widgets
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	pb.widgets = {};

	pb.widget = 
	{
		id : null,

		extend : function( options )
		{
			$.extend( this, options );

			pb.addAction( 'widget/type=' + this.id        , this.widget );
			pb.addAction( 'widgetAdded/type=' + this.id   , this.widgetAdded );
			pb.addAction( 'widgetUpdated/type=' + this.id , this.widgetUpdated );
			pb.addAction( 'widgetRemoved/type=' + this.id , this.widgetRemoved );
			pb.addAction( 'widgetSettings/type=' + this.id, this.widgetSettings );
		},

		widget : function( $widget )
		{

		},

		widgetAdded : function( $widget )
		{

		},

		widgetUpdated : function( $widget )
		{

		},

		widgetRemoved : function( $widget )
		{

		},

		widgetSettings : function( $content, $widget )
		{

		},
	};

})( jQuery, window, undefined );

/**
 * Column Widget
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};
	
	function updateCSSClass( $widget )
	{
		/**
		 * Remove all grid related classes
		 */

		var pattern = ''
			+ '\\b'						// boundary
			+ '('						// open capture
			+ 'pb-'						// class prefix
			+ '(?:offset|col|order)'	// property
			+ '(?:-(?:sm|md|lg|xl))?'   // breakpoint (optional)
			+ '(?:-\\d+)?'				// value (optional)
			+ ')'						// close capture
			+ '\\b';					// boundary

		var regExp = new RegExp( pattern, 'g' );
		
		$widget.removeClass( function( index, className )
		{
			var matches = className.match( regExp );

			return matches ? matches.join( ' ' ) : '';
		});

		/**
		 * Add grid classes
		 */

		var model = pb.getWidgetModel( $widget );

		// Offset

		var offset = model.data.responsiveness.offset_sm;

		if ( offset ) 
		{
			$widget.addClass( 'pb-offset-sm-' + offset );
		};

		// Cols

		var cols = model.data.cols;

		if ( cols ) 
		{
			$widget.addClass( 'pb-col-sm-' + cols );
		};

		// Order

		var order = model.data.responsiveness.order_sm;

		if ( order ) 
		{
			$widget.addClass( 'pb-order-sm-' + order );
		};
	}

	var column = pb.widget.extend(
	{
		id : 'column',

		widget : function( $widget )
		{
			updateCSSClass( $widget );

			// Sorting
			$widget.find( '> .pb-widget-inside > .pb-widget-container' ).sortable(
			{
				placeholder: 'pb-widget-placeholder',
				items: '> .pb-widget',
				handle: '> .pb-widget-top > .pb-widget-title',
				cursor: 'move',
				distance: 2,
				containment: '#wpwrap',
				tolerance: 'pointer',
				refreshPositions: true,
				connectWith : '.pb-column-widget > .pb-widget-inside > .pb-widget-container',
			});
		},

		widgetUpdated : function( $widget )
		{
			updateCSSClass( $widget );
		},
	});

	pb.widgets.column = column;

})( jQuery, window, undefined );

/**
 * Row Widget
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	function gcd( a, b )
	{
		return b ? gcd( b, a % b ) : a;
	}

	function reduceFraction( numerator, denominator )
	{
  		var _gcd = gcd( numerator, denominator );
  		
  		return [ numerator / _gcd, denominator / _gcd ];
	}

	function toFraction( numerators, denominator, reduce )
	{
		var numerator, fraction, fractions = [];

		for ( var i in numerators )
		{
			numerator = numerators[i];

			if ( reduce ) 
			{
				fraction = reduceFraction( numerator, denominator );
			}

			else
			{
				fraction = [ numerator, denominator ];
			}

			fractions.push( fraction[0] + '/' + fraction[1] );
		}
		
		return fractions;
	}

	function getLayout( $row )
	{
		var layout = [];

		// Loop columns
		$row.find( '> .pb-widget-inside > .pb-widget-container > .pb-column-widget' ).each( function()
		{
			// Get column width value

			var model = pb.getWidgetModel( this );
			
			// Add to layout
			layout.push( model.data.cols );
		});

		// Return
		return layout;
	}

	function setLayout( layout, $row )
	{
		var $columns = $row.find( '> .pb-widget-inside > .pb-widget-container > .pb-column-widget' );

		$.each( layout, function( i, cols )
		{
			var $column = $columns.eq( i );

			// Create

			if ( ! $column.length ) 
			{
				$column = pb.createWidget( 
				{
					type : 'column',
					data : { cols : cols },
				});

				var $prevColumn = $row.find( '> .pb-widget-inside > .pb-widget-container > .pb-column-widget' ).eq( i - 1 );

				// Add column
				pb.addWidget( $column, $prevColumn, 'insertAfter' );
			}

			// Update

			else
			{
				pb.updateWidget( $column,
				{
					cols : cols,
				});
			}
		});

		// Delete

		$columns.slice( layout.length ).each( function()
		{
			pb.removeWidget( this );
		});
	}

	function parseLayout( string, options )
	{
		// Options

		var defaults = 
		{
			min : 1,
			max : 12,
			sep : '+'
		};

		options = $.extend( {}, defaults, options );

		// Parse

		var layout = [];

		$.each( String( string ).split( options.sep ), function( i, cols )
		{
			// Remove surrounding whitespace
			cols = $.trim( cols );

			// Convert fraction
			if ( /^\d+\/\d+$/.test( cols ) ) 
			{
				var parts = cols.split( '/' );

				cols = options.max * ( parts[0] / parts[1] );
			}

			// Make integer
			cols = parseInt( cols );

			// Check if number
			if ( isNaN( cols ) ) 
			{
				return true;
			}

			// check bounds
			if ( cols < options.min ) 
			{
				cols = options.min;
			}

			else if ( cols > options.max )
			{
				cols = options.max;
			}

			// Add to layout
			layout.push( cols );
		});

		// Return
		return layout;
	}

	var row = pb.widget.extend(
	{
		id : 'row',
		
		widget : function( $widget )
		{
			// Set css class
			$widget.find( '> .pb-widget-inside > .pb-widget-container' )
				.addClass( 'pb-row' );
				
			// Sorting
			$widget.find( '> .pb-widget-inside > .pb-widget-container' ).sortable(
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
		},

		widgetSettings : function( $content, $widget )
		{
			// Layout

			var layout = getLayout( $widget );
			var $field = $content.find( '#pb_input-row_layout' );

			layout = toFraction( layout, 12, true );

			$field.val( layout.join( '+' ) );

			$content.on( 'click', '.pb-layout-control', function( event )
			{
				var layout = $( this ).data( 'layout' );

				layout = parseLayout( layout );
				layout = toFraction( layout, 12, true );

				$field.val( layout.join( '+' ) );
			});
		},

		widgetUpdated : function( $widget )
		{
			var model = pb.getWidgetModel( $widget );

			var layout = parseLayout( model.data.layout );

			// One column is required
			if ( ! layout.length ) 
			{
				layout.push( 12 );
			}

			setLayout( layout, $widget );
		},
	});

	pb.widgets.row = row;

})( jQuery, window, undefined );
