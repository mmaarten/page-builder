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
				$column.insertAfter( $prevColumn );

				pb.doAction( 'widgetAdded', $column );
				pb.doAction( 'widgetAdded/type=' + $column.data( 'type' ), $column );
			}

			// Update

			else
			{
				var model = pb.getWidgetModel( $column );

				if ( model.data.cols != cols ) 
				{
					model.data.cols = cols;

					pb.models[ model.id ] = model;

					pb.doAction( 'widgetUpdated', $column );
					pb.doAction( 'widgetUpdated/type=' + $column.data( 'type' ), $column );
				};
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
