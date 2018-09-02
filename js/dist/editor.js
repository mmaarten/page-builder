/* Simple JavaScript Inheritance
 * By John Resig https://johnresig.com/
 * MIT Licensed.
 */
// Inspired by base2 and Prototype
(function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;
 
  // The base Class implementation (does nothing)
  this.Class = function(){};
   
  // Create a new Class that inherits from this class
  Class.extend = function(prop) {
    var _super = this.prototype;
     
    // Instantiate a base class (but only create the instance,
    // don't run the init constructor)
    initializing = true;
    var prototype = new this();
    initializing = false;
     
    // Copy the properties over onto the new prototype
    for (var name in prop) {
      // Check if we're overwriting an existing function
      prototype[name] = typeof prop[name] == "function" && 
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn){
          return function() {
            var tmp = this._super;
             
            // Add a new ._super() method that is the same method
            // but on the super-class
            this._super = _super[name];
             
            // The method only need to be bound temporarily, so we
            // remove it when we're done executing
            var ret = fn.apply(this, arguments);        
            this._super = tmp;
             
            return ret;
          };
        })(name, prop[name]) :
        prop[name];
    }
     
    // The dummy class constructor
    function Class() {
      // All construction is actually done in the init method
      if ( !initializing && this.init )
        this.init.apply(this, arguments);
    }
     
    // Populate our constructed prototype object
    Class.prototype = prototype;
     
    // Enforce the constructor to be what we expect
    Class.prototype.constructor = Class;
 
    // And make this class extendable
    Class.extend = arguments.callee;
     
    return Class;
  };
})();
// https://github.com/carldanley/WP-JS-Hooks
( function( window, undefined ) {
	"use strict";

	/**
	 * Handles managing all events for whatever you plug it into. Priorities for hooks are based on lowest to highest in
	 * that, lowest priority hooks are fired first.
	 */
	var EventManager = function() {
		/**
		 * Maintain a reference to the object scope so our public methods never get confusing.
		 */
		var MethodsAvailable = {
			removeFilter : removeFilter,
			applyFilters : applyFilters,
			addFilter : addFilter,
			removeAction : removeAction,
			doAction : doAction,
			addAction : addAction,
			storage : getStorage
		};

		/**
		 * Contains the hooks that get registered with this EventManager. The array for storage utilizes a "flat"
		 * object literal such that looking up the hook utilizes the native object literal hash.
		 */
		var STORAGE = {
			actions : {},
			filters : {}
		};
		
		function getStorage() {
			
			return STORAGE;
			
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
			var args = Array.prototype.slice.call( arguments );
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
			var args = Array.prototype.slice.call( arguments );
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
			if ( !STORAGE[ type ][ hook ] ) {
				return;
			}
			if ( !callback ) {
				STORAGE[ type ][ hook ] = [];
			} else {
				var handlers = STORAGE[ type ][ hook ];
				var i;
				if ( !context ) {
					for ( i = handlers.length; i--; ) {
						if ( handlers[i].callback === callback ) {
							handlers.splice( i, 1 );
						}
					}
				}
				else {
					for ( i = handlers.length; i--; ) {
						var handler = handlers[i];
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
			var handlers = STORAGE[ type ][ hook ];
			
			if ( !handlers ) {
				return (type === 'filters') ? args[0] : false;
			}

			var i = 0, len = handlers.length;
			if ( type === 'filters' ) {
				for ( ; i < len; i++ ) {
					args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			} else {
				for ( ; i < len; i++ ) {
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
(function( $ )
{
	"use strict";

	var pb = 
	{
		$elem : null,
		$source : null,

		init : function( elem, options )
		{
			this.$elem = $( elem );
			this.o     = $.extend( {}, this.defaultOptions, options );

			this.$source = this.$elem.find( '.pb-source' );

			var _this = this;

			/**
			 * Saving
			 * -----------------------------------------------------------
			 */

			this.$elem.closest( 'form' ).on( 'submit', function( event )
			{
				_this.save();
			});

			/**
			 * Sorting
			 * -----------------------------------------------------------
			 */

			this.$elem.on( 'sortupdate', '.pb-widget-container', function( event, ui )
			{
				var $container = $( event.target ),
					$widget = $( ui.item );

				_this.doWidgetAction( 'widgetSortUpdate', $widget, $container );
			});

			this.$elem.on( 'sortreceive', '.pb-widget-container', function( event, ui )
			{
				var $container = $( event.target ),
					$widget = $( ui.item );

				_this.doWidgetAction( 'widgetSortReceive', $widget, $container );
			});

			this.$elem.on( 'sortremove', '.pb-widget-container', function( event, ui )
			{
				var $container = $( event.target ),
					$widget = $( ui.item );

				_this.doWidgetAction( 'widgetSortRemove', $widget, $container );
			});

			this.$elem.find( '.pb-main-widget-container' ).sortable( 
			{
				handle : '> .pb-widget-top',
				placeholder: 'pb-sortable-placeholder',
				items: '> .pb-widget',
				cursor: 'move',
				distance: 2,
				tolerance: 'pointer',
				refreshPositions: true,
				forcePlaceholderSize : true
			});

			/**
			 * Controls
			 * -----------------------------------------------------------
			 */

			this.$elem.on( 'click', '.pb-widget-control', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );

				_this.doControlAction( 'controlClick', $( this ), $widget );
			});

			// Toggle

			pb.$elem.on( 'click', '.pb-widget-control:not([data-type="toggle"])', function( event )
			{
				event.stopPropagation();
			});

			pb.$elem.on( 'click', '.pb-widget-top', function( event )
			{
				var $widget = $( this ).closest( '.pb-widget' );

				$widget.find( '> .pb-widget-inside' ).slideToggle( 'fast', function()
				{
					$widget.toggleClass( 'open' );
				});
				
			});

			// Main add widget control

			this.$elem.on( 'click', '.pb-add-widget-control', function( event )
			{
				_this.widgetPicker( function( $choosen )
				{
					_this.$elem.find( '.pb-main-widget-container' ).append( $choosen );

					_this.doWidgetAction( 'widgetAdd', $choosen );
					_this.loadWidgetPreview( $choosen );
				});
			});

			/**
			 * Clients Init
			 * -----------------------------------------------------------
			 */

			this.doAction( 'init' );

			/**
			 * Loading
			 * -----------------------------------------------------------
			 */

			this.load();
			this.loadWidgetPreview();
		},

		/**
		 * Options
		 * -----------------------------------------------------------
		 */

		o : {},

		defaultOptions :
		{
			nonce : null,
			nonceName: null,
			widgetDefaults : {},
			confirmDelete : 'Are you sure you want to delete this widget?'
		},

		get : function( k )
		{
			if ( typeof this.o[ k ] !== 'undefined' ) 
			{
				return this.o[ k ];
			}

			return null;
		},

		set : function( k, v )
		{
			var o = {};

			if ( typeof k === 'object' ) 
			{
				o = k;
			}

			else
			{
				o[ k ] = v;
			}

			$.extend( this.o, o );
		},

		/**
		 * Event Manager
		 * -----------------------------------------------------------
		 */

		addAction : function()
		{
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.addAction.apply( this, arguments );
		},

		removeAction : function()
		{
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.removeAction.apply( this, arguments );
		},

		doAction : function()
		{
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.doAction.apply( this, arguments );
		},

		addFilter : function()
		{
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.addFilter.apply( this, arguments );
		},

		removeFilter : function()
		{
			arguments[0] = 'pb/' + arguments[0];

			wp.hooks.removeFilter.apply( this, arguments );
		},

		applyFilters : function()
		{
			arguments[0] = 'pb/' + arguments[0];

			return wp.hooks.applyFilters.apply( this, arguments );
		},

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		/**
		 * Load
		 * 
		 * Reads source and creates widgets.
		 */
		load : function()
		{
			var _this = this,
				source = this.$source.val();

			// removes all models and widgets
			
			this.models = {};
			this.$elem.find( '.pb-main-widget-container' ).empty();

			// Creates widgets from source data 

			var models = $.extend( {}, JSON.parse( source ) );

			$.each( models, function( i, model )
			{
				var $widget = _this.createWidget( model ), 
					$parent = _this.$elem.find( '.pb-main-widget-container' );

				if ( model.parent ) 
				{
					$parent = _this.$elem.find( '.pb-main-widget-container .pb-widget' ).filter( function()
					{
						return $( this ).data( 'model' ) == model.parent;

					}).find( '> .pb-widget-inside > .pb-widget-container' );
				}

				$parent.append( $widget );
			});

			pb.doAction( 'load' );
		},

		/**
		 * Save
		 * 
		 * Writes models to source.
		 */
		save : function()
		{
			var _this = this,
				models = {};

			this.$elem.find( '.pb-main-widget-container .pb-widget' ).each( function()
			{
				var $widget = $( this ),
					model = $.extend( {}, _this.models[ $widget.data( 'model' ) ] ),
					$parent = $widget.parent().closest( '.pb-widget' );

					model.index  = $widget.index;
					model.parent = $parent.length ? $parent.data( 'model' ) : ''

					models[ model.id ] = model;
			});

			this.models = models;

			// Writes models into textarea

			this.$source.val( JSON.stringify( this.models ) );

			pb.doAction( 'save' );
		},

		/**
		 * Utils
		 * -----------------------------------------------------------
		 */

		generateId : function()
		{
			function s4()
			{
			    return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
			}

			return s4() + s4() + s4() + s4();
		},

		stripSlashes : function( obj )
		{
			obj = $.extend( {}, obj );

			$.each( obj, function( key, value )
			{
				if ( typeof value === 'string' ) 
				{
					value = value.replace( /\\(.)/mg, '$1' );
				}

				else if ( typeof value === 'object' )
				{
					value = pb.stripSlashes( value );
				}

				obj[ key ] = value;
			});

			return obj;
		},

		prepareAjax : function( data )
		{
			data = $.extend( {}, data );

			// Adds nonce

			var nonce     = this.get( 'nonce' );
			var nonceName = this.get( 'nonceName' );

			data[ nonceName ] = nonce;

			return data;
		},

		/**
		 * Reduce Fraction
		 *
		 * Reduces a fraction by finding the Greatest Common Divisor and dividing by it.
		 *
		 * @link https://stackoverflow.com/questions/4652468/is-there-a-javascript-function-that-reduces-a-fraction
		 * @return array e.g. [ {numerator}, {denominator} ]
		 */
		reduceFraction : function( numerator, denominator )
		{
	  		var gcd = function gcd( a , b ) 
	  		{
	    		return b ? gcd( b, a % b ) : a;
	  		};

	  		gcd = gcd(numerator,denominator);
	  		
	  		return [ numerator / gcd, denominator / gcd ];
		},

		/**
		 * Controls
		 * -----------------------------------------------------------
		 */

		doControlAction : function( action, $control )
		{
			var actions = 
			[
				action,
				action + '/type='  + $control.data( 'type' ),
			];

			for ( var i in actions )
			{
				arguments[0] = actions[i];

				pb.doAction.apply( this, arguments );
			}
		},

		/**
		 * Models
		 * -----------------------------------------------------------
		 */

		models : {},

		createModel : function( args )
		{
			// checks if args is type parameter

			if ( args && typeof args !== 'object' ) 
			{
				args = { type : args };
			}

			else
			{
				args = $.extend( {}, args );
			};

			//

			var defaults = 
			{
				id : this.generateId(),
				type : '',
				data : {}
			};

			// Gets widget default data

			if ( typeof args.type !== 'undefined' && args.type ) 
			{
				var defaultData = this.get( 'widgetDefaults' );

				if ( typeof defaultData[ args.type ] !== 'undefined' && defaultData[ args.type ] )
				{
					defaults.data = $.extend( {}, defaults.data, defaultData[ args.type ] )
				}
			}

			// Creates model

			var model = $.extend( {}, defaults, args );

			return model;
		},

		/**
		 * Widgets
		 * -----------------------------------------------------------
		 */

		doWidgetAction : function( action, $widget )
		{
			var actions = 
			[
				action,
				action + '/type='  + $widget.data( 'type' ),
				action + '/model=' + $widget.data( 'model' ),
			];

			for ( var i in actions )
			{
				arguments[0] = actions[i];

				pb.doAction.apply( this, arguments );
			}
		},

		createWidget : function( args )
		{
			// Creates model
			
			var model = this.createModel( args );

			this.models[ model.id ] = model;

			// Creates widget

			var $widget = this.$elem.find( '.pb-available-widgets .pb-widget' ).filter( function()
			{
				return $( this ).data( 'type' ) == model.type;

			}).clone();

			// sets widget - model relation

			$widget.attr( 'data-model', model.id );

			//

			this.doWidgetAction( 'widget', $widget );

			return $widget;
		},

		duplicateWidget : function( widget )
		{
			var $copy = $( widget ).clone(), 
			    _this = this;

			// Copies models

			$copy.find( '.pb-widget' ).andSelf().each( function()
			{
				var $widget = $( this ),
				    model = _this.models[ $widget.data( 'model' ) ];

				// removes data and event handlers
				$widget.removeData().off();

				var modelCopy = $.extend( {}, model, 
				{
					id : pb.generateId()
				});

				_this.models[ modelCopy.id ] = modelCopy;

				$widget.attr( 'data-model', modelCopy.id );

				_this.doWidgetAction( 'widget', $widget );
			});

			return $copy;
		},

		removeWidget : function( widget )
		{
			var $widget = $( widget ),
				$parent = $widget.parent().closest( '.pb-widget' ),
			    index   = $widget.index(),
			    model   = $.extend( {}, this.models[ $widget.data( 'model' ) ] ),
			    _this   = this;

			// Removes child widgets

			$widget.find( '.pb-widget' ).each( function()
			{
				_this.removeWidget( this );
			});

			// Removes widget

			delete this.models[ model.id ];

			$widget
				.removeAttr( 'data-model' )
				.remove();

			this.doWidgetAction( 'widgetRemove', $widget, model, $parent, index );
		},

		widgetSettings : function( widget )
		{
			var _this = this,
				$widget = $( widget ),
				model = this.models[ $widget.data( 'model' ) ],
				data  = $.extend( {}, model.data );

			// Loads page

			$widget.addClass( 'loading' );

			$.extend( data, 
			{
				action : 'pb_widget_settings',
				widget : model.type
			});

			$.ajax(
			{
				url : ajaxurl,
				method : 'POST',
				data : this.prepareAjax( data ),
				success : function( content )
				{
					// Opens modal

					$.featherlight( content, 
					{
						namespace : 'pb-modal',

						afterContent: function()
						{
							var modal = this;

							// Form submit

							this.$content.on( 'submit', 'form', function( event )
							{
								event.preventDefault();

								var $form = $( this );
								var $fields = $form.find( ':input:not([disabled])' );

								// Sanitizes user input

								$.ajax(
								{
									url : ajaxurl,
									method : 'POST',
									data : $( this ).serialize(),
									beforeSend : function()
									{
										$form.addClass( 'pb-loading' );
										$fields.prop( 'disabled', true );
									},
									success : function( sanitized )
									{
										sanitized = pb.stripSlashes( sanitized );

										// Updated model data

										_this.models[ model.id ] = $.extend( {}, model, 
										{
											data : $.extend( {}, sanitized )
										});

										_this.doWidgetAction( 'widgetSettingsSubmit', $widget, modal.$content );
										_this.doWidgetAction( 'widgetUpdate', $widget );

										_this.loadWidgetPreview( $widget );

										// closed modal
										modal.close();
									},
									complete : function()
									{
										$fields.prop( 'disabled', false );
										$form.removeClass( 'pb-loading' );
									},
								});
							});

							_this.doWidgetAction( 'widgetSettings', $widget, this.$content );
						},

						afterClose : function()
						{
							_this.doWidgetAction( 'widgetSettingsClose', $widget, this.$content );
						}
					});
				},

				complete : function()
				{
					$widget.removeClass( 'loading' );
				}
			});
		},

		widgetPicker : function( callback, parentWidget )
		{
			var _this = this, parentType;

			var parentType = '';

			if ( $( parentWidget ).is( '.pb-widget' ) ) 
			{
				var model = pb.models[ $( parentWidget ).data( 'model' ) ];

				parentType = model.type;
			}

			// Loads picker

			$.ajax(
			{
				url : ajaxurl,
				method : 'POST',
				data : this.prepareAjax( 
				{
					action : 'pb_widget_picker',
					parent : parentType
				}),
				success : function( content )
				{
					// Opens modal

					$.featherlight( content, 
					{
						namespace : 'pb-modal',

						afterContent: function()
						{
							var modal = this;

							// Sets modal id

							this.$content.closest( '.pb-modal' ).attr( 'id', 'pb-widget-picker-modal' );

							// Equal button height

							this.$content.find( '.pb-widget-button' ).matchHeight(
							{
								byRow : true
							});

							// Widget button click

							this.$content.on( 'click', '.pb-widget-button', function( event )
							{
								// Gets widget type

								var type = $( this ).data( 'type' );

								// Creates choosen widget

								var $widget = _this.createWidget( type );

								// callback

								picker.doCallback( $widget );

								// closes modal

								modal.close();
							});

							var picker = 
							{
								doCallback : function( $widget )
								{
									callback( $widget );

									modal.close();
								}
							};

							pb.doAction( 'widgetPicker', parentType, this.$content, picker );
						}
					});
				}
			});
		},

		loadWidgetPreview : function( widget )
		{
			var $widgets, models = [], _this = this;

			/**
			 * gets widgets
			 * -------------------------------------------------------
			 */

			// Specific widgets

			if ( typeof widget !== 'undefined' ) 
			{
				$widgets = $( widget );
			}

			// All widgets

			else
			{
				$widgets = this.$elem.find( '.pb-main-widget-container .pb-widget' );
			}

			/**
			 * Gets widgets models
			 * -------------------------------------------------------
			 */

			$widgets.each( function()
			{
				var $widget = $( this ),
					model = $.extend( {}, _this.models[ $widget.data( 'model' ) ] );

				models[ model.id ] = model;
			});

			/**
			 * Creates batches
			 * -------------------------------------------------------
			 */

			models = Object.values( models );

			var bulk = [], length = 10, _models, ajax;

			for ( var i = 0; i < models.length; i += length ) 
			{
				_models = models.slice( i, i + length );

				ajax = $.ajax(
				{
					url : ajaxurl,
					method : 'POST',
					data : this.prepareAjax(
					{
						action : 'pb_widget_preview',
						models : _models
					})
				});

				bulk.push( ajax );
			}

			// Checks if there is any data to load

			if ( ! bulk.length ) 
			{
				return;
			}

			/**
			 * Processes batches
			 * -------------------------------------------------------
			 */

			$widgets.addClass( 'loading' );

			$.when.apply( this, bulk ).then( function()
			{
				var args = arguments;

				if ( bulk.length == 1 ) 
				{
					args = [ args ];
				}

				$.each( args, function( i )
				{
					var preview = args[i][0];

					$.each( preview, function( modelId, content )
					{
						var $widget = $widgets.filter( function()
						{
							return $( this ).data( 'model' ) == modelId;
						});

						$widget.find( '> .pb-widget-inside > .pb-widget-preview' )
							.html( content );
				        
					});
				});

				$widgets.removeClass( 'loading' );
			});
		}
	};

	window.pb = pb;

})( jQuery );

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

(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var editor = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'editor' );
		},

		field : function( $field )
		{
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

			var fieldOptions = $.extend( {}, pb.get( 'editor_field_' + $field.data( 'key' ) ) );


			// mce init

			var init = $.extend( {}, defaults, fieldOptions );

			init.id = newId;
			init.selector = '#' + newId;

			// Stores settings
			tinyMCEPreInit.mceInit[ newId ] = init;

			tinymce.init( init );
		},

		updateInput : function( $field )
		{
			// TODO : Calls the save method on ALL editor instances in the collection
			tinymce.triggerSave();
		},

		destroy : function( $field )
		{
			var $textarea = $field.find( 'textarea' );

			var newId = $textarea.attr( 'id' ).replace( /-/g, '' );

			var instance = tinymce.get( newId );

			if ( instance ) 
			{
				//tinymce.execCommand( 'mceRemoveControl', true, newId );

				instance.destroy();

				instance = null;
			};

			delete tinyMCEPreInit.mceInit[ newId ];
		}
	});

	pb.fields.editor = new editor();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var icon = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'icon' );
		},

		field : function( $field )
		{
			$field.on( 'click', '.pb-icon-set', function( event )
			{
				$.ajax(
				{
					url : ajaxurl,
					method : 'POST',
					data : pb.prepareAjax(
					{
						action : 'pb_icon_picker',
						active : $field.find( ':input' ).val()
					}),
					success : function( content )
					{
						$.featherlight( content, 
						{
							namespace : 'pb-modal',
							afterContent : function()
							{
								var modal = this;

								this.$content.on( 'input', '.pb-icon-picker-search', function( event )
								{
									var search = $( this ).val();

									search = $.trim( search );
									search = search.toLowerCase();

									modal.$content.find( '.pb-icon-picker-icon' ).show();

									if ( search ) 
									{
										var $exclude = modal.$content.find( '.pb-icon-picker-icon' ).filter( function()
										{
											var term = $( this ).data( 'term' ) || '';

											return term.indexOf( search ) == -1;
										
										}).hide();
									}
								});

								this.$content.on( 'click', '.pb-icon-picker-icon', function()
								{
									var id = $( this ).data( 'id' );
									var html = $( this ).html();

									$field.find( ':input' ).val( id );
									$field.find( '.pb-icon-preview' ).html( html );

									$field.addClass( 'pb-has-value' );

									modal.close();
								});
							}
						});
					}
				})
			});

			$field.on( 'click', '.pb-icon-unset', function( event )
			{
				$field.find( ':input' ).val( '' );
				$field.find( '.pb-icon-preview' ).html( '' );

				$field.removeClass( 'pb-has-value' );
			});
		}
	});

	pb.fields.icon = new icon();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var image = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'image' );
		},

		field : function( $field )
		{
			var update = function()
			{
				var $items     = $field.find( '.pb-image-picker-items .pb-image-picker-item' );
				var $addButton = $field.find( '.pb-image-picker-add' );
				var multiple   = $field.find( '.pb-image-picker' ).data( 'multiple' ) ? true : false;

				if ( ! multiple && $items.length ) 
				{
					$addButton.hide();
				}

				else
				{
					$addButton.show();
				};
			};

			var createItem = function( attachment )
			{
				var imageSize;

				if ( typeof attachment.sizes.thumbnail !== 'undefined' ) 
				{
					imageSize = attachment.sizes.thumbnail;
				}

				else
				{
					imageSize = attachment.sizes.full;
				}

				var $item = $field.find( '.pb-clone .pb-image-picker-item' ).clone( false ).removeClass( 'pb-clone' );

				$item.find( ':input' ).val( attachment.id );
				$item.find( 'img' ).attr( 'src', imageSize.url );

				return $item;
			};
			
			var multiple = $field.find( '.pb-image-picker' ).data( 'multiple' ) ? true : false;

			if ( multiple ) 
			{
				$field.find( '.pb-image-picker-items' ).sortable(
				{
					cursor: 'move',
					distance: 2,
					tolerance: 'pointer',
					refreshPositions: true,
					forcePlaceholderSize : true
				});
			}

			var frame, _this = this;

			$field.on( 'click', '.pb-image-picker-add-control', function( event )
			{
				event.preventDefault();

				// If the media frame already exists, reopen it.

			    if ( frame ) 
			    {
			    	frame.open();

			    	return;
			    }

				// Create a new media frame

			    frame = wp.media(
			    {
					title    : 'Choose Image',
					button   : { text: 'Insert Image' },
					library  : { type: [ 'image' ] },
			     	multiple : multiple
			    });
			    
			    // When an image is selected in the media frame...

			    frame.on( 'select', function( event ) 
			    {
					// Get attachments

					var attachments = frame.state().get('selection').toJSON();

					// Creates items and add them to the DOM

					jQuery.each( attachments, function( i, attachment )
					{
						var $item = createItem( attachment );

						$field.find( '.pb-image-picker-items' )
							.append( $item );
					});

					update();
			    });

			    // Opens frame

			    frame.open();
			});

			$field.on( 'click', '.pb-image-picker-delete-control', function( event )
			{
				event.preventDefault();

				var $item = jQuery( this ).closest( '.pb-image-picker-item' );

				$item.remove();

				update();
			});

			update();
		}
	});

	pb.fields.image = new image();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var post = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'post' );
		},

		field : function( $field )
		{
			$field.find( 'select' ).select2(
			{
				ajax: 
				{
					url      : window.ajaxurl,
					dataType : 'json',
					delay    : 250,
					method   : 'POST',

					data: function ( params ) 
					{
						var $select = jQuery( this.context );

						return pb.prepareAjax(
						{
							action : 'pb_post_field_get_choices',
							field  : $field.data( 'key' ),
							page   : $field.data( 'page' ),
							search : params.term,
							paged  : params.page
						});
					},

					processResults: function ( data, params ) 
					{
						return {
							results : data.items,
							pagination:
							{
								more: data.paged < data.max_num_pages
					        }
						};
					}
				}
			});
		}
	});

	pb.fields.post = new post();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var repeater = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'repeater' );
		},

		field : function( $field )
		{
			var $elem = $field.find( '> .pb-input > .pb-repeater' );

			var $rows = $elem.find( '> .pb-repeater-table > .pb-repeater-rows' );

			var i = $rows.find( '> .pb-repeater-row' ).length - 1;

			// Init sub fields
			$rows.find( '> .pb-repeater-row:not(.pb-clone) > .pb-field' ).each( function()
			{
				pb.fields.doFieldAction( 'field', $( this ) );
			});

			$rows.sortable(
			{
				items: '> .pb-repeater-row:not(.pb-clone)',
				cursor: 'move',
				distance: 2,
				tolerance: 'pointer',
				refreshPositions: true,
				forcePlaceholderSize : true
			});

			$elem.on( 'click', '> .pb-repeater-footer .pb-repeater-add', function( event )
			{
				// Creates row

				var $row = $rows.find( '> .pb-repeater-row.pb-clone' ).clone( false ).removeClass( 'pb-clone' );

				i++;

				// Updates row input fields

				$row.find( ':input' ).each( function()
				{
					var $input = $( this );

					var replacements = 
					[
						{ attr : 'name', find : '[0]', replacement : '[' + i + ']' },
						{ attr : 'id'  , find : '-0-', replacement : '-' + i + '-' }
					];

					$.each( replacements, function()
					{
						var value = $input.attr( this.attr );

						if ( typeof value === 'undefined' ) 
						{
							return true;
						}

						var pos = value.indexOf( this.find );

						if ( pos == -1 ) 
						{
							return true;
						}

						var before = value.substring( 0, pos );
						var after  = value.substring( pos + this.find.length );

						var newValue = before + this.replacement + after;

						$input.attr( this.attr, newValue );
					});
				});

				// Adds row to DOM

				$rows.append( $row );

				// Init sub fields

				$row.find( '> .pb-field' ).each( function()
				{
					pb.fields.doFieldAction( 'field', $( this ) );
				});
			});

			$elem.on( 'click', '.pb-repeater-remove', function( event )
			{
				var $row = jQuery( this ).closest( '.pb-repeater-row' );

				$row.remove();
			});
		}
	});

	pb.fields.repeater = new repeater();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	pb.addAction( 'widgetSettings', function( $widget, $page )
	{
		$page.find( '.pb-fields, .pb-sub-fields' ).each( function()
		{
			// selects all children from the first tab
			// uses children() cause there can be other elements than '.pb-field'

			var $fields = $( this ).children().filter( '.pb-field-type-tab' ).first().nextAll().andSelf();

			if ( ! $fields.length ) 
			{
				return true;
			}

			var $tabFields = $fields.filter( '.pb-field-type-tab' );

			var setActiveTab = function( tab )
			{
				var $tab = $( tab );

				if ( $tab.is( '.nav-tab-active' ) ) 
				{
					return;
				};

				// Selects tab field and other fields until next tab field.

				var $_fields = $fields.filter( function()
				{
					return $( this ).data( 'type' ) == 'tab' 
					    && $( this ).data( 'key' ) == $tab.data( 'key' );

				}).nextUntil( '.pb-field[data-type="tab"]' );

				$_fields.show();

				$fields.not( $_fields ).hide();

				// Updates nav

				$nav.find( '.nav-tab-active' ).removeClass( 'nav-tab-active' );

				$tab.addClass( 'nav-tab-active' );
			};

			// Tab Nav

			var $nav = $( '<h2 class="nav-tab-wrapper"></h2>' );

			$tabFields.each( function()
			{
				var $field = $( this );

				var $item = $( '<a class="nav-tab"></a>' );

				$item
					.text( $field.find( '.pb-input-label' ).text() )
					.attr( 'href', '' ) // cursor pointer on hover
					.attr( 'data-key', $field.data( 'key' ) )

				$nav.append( $item );
			});

			$nav.on( 'click', '.nav-tab', function( event )
			{
				event.preventDefault();

				setActiveTab( this );
			});

			// Inserts nav just before the first tab field

			$nav.insertBefore( $tabFields.first() );

			// Activates first tab

			setActiveTab( $nav.find( '.nav-tab' ).first() );
		});
	});
})( jQuery );

(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var term = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'term' );
		},

		field : function( $field )
		{
			$field.find( 'select' ).select2(
			{
				ajax: 
				{
					url      : window.ajaxurl,
					dataType : 'json',
					delay    : 250,
					method   : 'POST',

					data: function ( params ) 
					{
						var $select = jQuery( this.context );

						return pb.prepareAjax(
						{
							action : 'pb_term_field_get_choices',
							field  : $field.data( 'key' ),
							page   : $field.data( 'page' ),
							search : params.term,
							paged  : params.page
						});
					},

					processResults: function ( data, params ) 
					{
						return {
							results : data.items,
							pagination:
							{
								more: data.paged < data.max_num_pages
					        }
						};
					}
				}
			});
		}
	});

	pb.fields.term = new term();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var urlField = pb.field.extend( 
	{
		init : function()
		{
			this._super( 'url' );
		},

		field : function( $field )
		{
			$field.find( ':input[data-search="1"]' ).autocomplete(
			{
				minLength: 1,
				source: function( request, response ) 
				{
					$.ajax( 
					{
						url: ajaxurl,
						method : 'POST',
						data: pb.prepareAjax(
						{
							action : 'pb_url_autocomplete',
							term: request.term,
							field : $field.data( 'key' ),
							page  : $field.data( 'page' )
						}),
						success: function( data ) 
						{
							response( data );
						}
					});
				}
			})

			.autocomplete( 'instance' )._renderItem = function( ul, item ) 
			{
				var $li = $( '<li>' );

				var $title = $( '<strong></strong>' ).text( item.label );
				var $description = $( '<em></em>' ).text( item.url );

				$li.append( $title );
				$li.append( '<br>' );
				$li.append( $description );

				return $li.appendTo( ul );
		    }
		}
	});

	pb.fields.url = new urlField();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	pb.widget = Class.extend(
	{
		id : null,

		init : function( id )
		{
			this.id = id;

			pb.addAction( 'widget/type=' + this.id 		     , this.widget );
			pb.addAction( 'widgetAdd/type=' + this.id        , this.widgetAdd );
			pb.addAction( 'widgetUpdate/type=' + this.id     , this.widgetUpdate );
			pb.addAction( 'widgetRemove/type=' + this.id     , this.widgetRemove );
			pb.addAction( 'widgetSettings/type=' + this.id   , this.widgetSettings );
			pb.addAction( 'widgetSettingsSubmit/type=' + this.id, this.widgetSettingsSubmit );
			pb.addAction( 'widgetSortUpdate/type=' + this.id , this.widgetSortUpdate );
			pb.addAction( 'widgetSortReceive/type=' + this.id, this.widgetSortReceive );
			pb.addAction( 'widgetSortRemove/type=' + this.id , this.widgetSortRemove );
		},

		widget : function( $widget )
		{
			
		},

		widgetAdd : function( $widget )
		{
			
		},

		widgetUpdate : function( $widget )
		{
			
		},

		widgetRemove : function( $widget, model, $parentWidget, index )
		{
			
		},

		widgetSettings : function( $widget, $content )
		{
			
		},

		widgetSettingsSubmit : function( $widget, $content )
		{

		},

		widgetSortUpdate : function ( $widget, $container )
		{

		},

		widgetSortReceive : function ( $widget, $container )
		{
			
		},

		widgetSortRemove : function ( $widget, $container )
		{
			
		}
	});

	pb.widgets = {};

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var row = pb.widget.extend( 
	{
		init : function()
		{
			this._super( 'row' );

			pb.addAction( 'widgetAdd', this.anyWidgetAdded );
		},

		getLayout : function( $widget, doFractions )
		{
			var layout = [];

			$widget.find( '> .pb-widget-inside > .pb-widget-container > .pb-column-widget' ).each( function()
			{
				var $column = $( this ),
				model = pb.models[ $column.data( 'model' ) ];

				var width = model.data.width || 12;

				if ( doFractions ) 
				{
					var fraction = pb.reduceFraction( model.data.width || 12, 12 );

					width = fraction[0] + '/' + fraction[1];
				}

				layout.push( width );
			});

			return layout;
		},

		setLayout : function( layout, $widget )
		{
			var current = this.getLayout( $widget, false );

			if ( current.join('+') == layout.join('+') ) 
			{
				return;
			}

			/**
			 * Removes columns
			 * -------------------------------------------------------
			 */

			$widget.find( '> .pb-widget-inside > .pb-widget-container' ).children().filter( function()
			{
				return jQuery( this ).index() > layout.length - 1;
			})

			.each( function()
			{
				pb.removeWidget( this );
			});

			/**
			 * Creates/updates columns
			 * -------------------------------------------------------
			 */

			jQuery.each( layout, function( i, width )
			{
				var $column = $widget.find( '> .pb-widget-inside > .pb-widget-container' ).children().eq( i );

				// create

				if ( ! $column.length ) 
				{
					$column = pb.createWidget(
					{
						type : 'column',
						data : { width : width }
					});

					$widget.find( '> .pb-widget-inside > .pb-widget-container' )
						.append( $column );

					pb.doWidgetAction( 'widgetAdd', $column );
				}

				// update

				else
				{
					var m = pb.models[ $column.data( 'model' ) ];

					// clears offset
					m.data.responsiveness = jQuery.extend( {}, m.data.responsiveness, { offset_md : '' } );
					
					m.data.width = width;

					console.log( width );

					pb.doWidgetAction( 'widgetUpdate', $column );
				};
			});
		},

		widget : function( $widget )
		{
			$widget.find( '> .pb-widget-inside > .pb-widget-container' )
				.addClass( 'pb-row' )
				.sortable( 
				{
					handle : '.pb-widget-top',
					placeholder: 'pb-sortable-placeholder',
					items: '> .pb-widget',
					cursor: 'move',
					distance: 2,
					tolerance: 'pointer',
					refreshPositions: true,
					forcePlaceholderSize : true
				});
		},

		anyWidgetAdded : function( $widget )
		{
			if ( $widget.data( 'type' ) == 'row' ) 
			{
				return;
			}

			var $parentWidget = $widget.parent().closest( '.pb-widget' );

			if ( $parentWidget.length ) 
			{
				return;
			}

			pb.removeAction( 'widgetAdd', pb.widgets.row.anyWidgetAdded );

			var $parent = $widget.parent();
			// TODO : $index  = $widget.index();

			var $row = pb.createWidget( 'row' );

			if ( $widget.data( 'type' ) == 'column' ) 
			{
				$row.find( '> .pb-widget-inside > .pb-widget-container' )
					.append( $widget );
			}

			else
			{
				var $column = pb.createWidget( 'column' );

				$row.find( '> .pb-widget-inside > .pb-widget-container' )
					.append( $column );

				$column.find( '> .pb-widget-inside > .pb-widget-container' )
					.append( $widget );
			}

			$parent.append( $row );

			pb.addAction( 'widgetAdd', pb.widgets.row.anyWidgetAdded );
		},

		widgetAdd : function( $widget )
		{
			// Adds column when empty

			if ( ! $widget.find( '.pb-widget' ).length ) 
			{
				var $column = pb.createWidget( 'column' );

				console.log( pb.models[ $column.data( 'model' ) ] );

				$widget.find( '> .pb-widget-inside > .pb-widget-container' ).append( $column );

				pb.doWidgetAction( 'widgetAdd', $column );
			}
		},

		widgetSettings : function( $widget, $content )
		{
			var picker = wp.template( 'pb-row-layout-picker' )();

			jQuery( picker ).insertBefore( $content.find( ':input#pb-input-layout' ) );

			/**
			 * Layout Picker
			 * -------------------------------------------------------
			 */

			(function()
			{
				var $picker = $content.find( '.pb-layout-picker' );
				var $target = jQuery( $picker.data( 'target' ) );

				function setLayout( layout )
				{
					$target.val( layout );

					var $button = $picker.find( 'button' ).filter( function()
					{
						return jQuery( this ).data( 'layout' ) == layout;
					});

					$picker.find( 'button' )
						.removeClass( 'active' );

					$button.addClass( 'active' );
				};

				$picker.on( 'click', 'button', function( event )
				{
					var layout = jQuery( this ).data( 'layout' );

					setLayout( layout );
				});

				$target.on( 'change', function( event )
				{
					var layout = jQuery( this ).val();

					setLayout( layout );
				});

				setLayout( $target.val() );

			})();

			/**
			 * Populates Layout field
			 * -------------------------------------------------------
			 */

			var layout = pb.widgets.row.getLayout( $widget, true );

			$content.find( ':input#pb-input-layout' )
				.val( layout.join( '+' ) )
				.trigger( 'change' )
		},

		widgetSettingsSubmit : function( $widget, $content )
		{
			/**
			 * Layout
			 * -------------------------------------------------------
			 */

			var layout = $content.find( ':input#pb-input-layout' ).val();

			layout = pb.widgets.row.sanitizeLayout( layout );

			pb.widgets.row.setLayout( layout.split( '+' ), $widget );
		},

		sanitizeLayout : function( layout )
		{
			var min = 1, max = 12;

			layout = layout.replace( /\++/g, '+' ); // removes double '+'
			layout = layout.replace( /(^\+)|(\+$)/g, '' ); // removes starting and ending '+'
			layout = layout.replace( /\s/g, '' ); // removes spaces

			if ( ! layout )
			{
				return String( max );
			};

			layout = layout.split( '+' );

			var sanitized = [];

			jQuery.each( layout, function( i, cols )
			{
				// converts fractions to numbers

				var matches = cols.match( /^(\d+)\/(\d+)$/ );

				if ( matches ) 
				{
					var numerator   = parseInt( matches[1] );
					var denominator = parseInt( matches[2] );

					cols = max * ( numerator / denominator );
				};

				// Checks if integer

				if ( cols != parseInt( cols, 10 ) )
				{
					cols = max;
				};

				// Checks range

				if ( cols < min ) 
				{
					cols = min;
				}

				else if ( cols > max )
				{
					cols = max;
				};

				sanitized.push( cols );
			});

			return sanitized.join( '+' );
		}
	});

	pb.widgets.row = new row();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var column = pb.widget.extend( 
	{
		init : function()
		{
			this._super( 'column' );

			pb.addAction( 'load', this.load );
		},

		load : function()
		{
			pb.$elem.find( '.pb-main-widget-container .pb-row-widget' ).each( function()
			{
				var $columns = $( this ).find( '> .pb-widget-inside > .pb-widget-container' ).children();

				if ( $columns.length == 1 ) 
				{
					$columns.find( '> .pb-widget-top .pb-widget-delete-control' ).hide();
				}
			});
		},

		widget : function( $widget )
		{
			$widget.find( '> .pb-widget-inside > .pb-widget-container' ).sortable( 
			{
				handle : '> .pb-widget-top',
				placeholder: 'pb-sortable-placeholder',
				items: '> .pb-widget',
				cursor: 'move',
				distance: 2,
				tolerance: 'pointer',
				refreshPositions: true,
				forcePlaceholderSize : true,
				// Connects with other columns
				connectWith : [ '.pb-column-widget > .pb-widget-inside > .pb-widget-container' ]
			});

			pb.widgets.column.updateCSSClasses( $widget );
		},

		widgetAdd : function( $widget )
		{
			pb.widgets.column.updateCSSClasses( $widget );

			if ( $widget.siblings().length ) 
			{
				$widget.siblings().find( '> .pb-widget-top .pb-widget-delete-control' ).show();
			}
		},

		widgetRemove : function( $widget, model, $parentWidget, index )
		{
			var $columns = $parentWidget.find( '> .pb-widget-inside > .pb-widget-container' ).children();

			if ( $columns.length == 1 ) 
			{
				$columns.find( '> .pb-widget-top .pb-widget-delete-control' ).hide();
			}
		},

		widgetUpdate : function( $widget )
		{
			pb.widgets.column.updateCSSClasses( $widget );
		},

		updateCSSClasses : function( $widget )
		{
			var model = pb.models[ $widget.data( 'model' ) ];

			// Removes previous set classes

			$widget.removeClass( function( i, className )
			{
				var classNames = className.split( ' ' ), remove = [];

				jQuery.each( classNames, function()
				{
					if ( /^(pb-col|pb-offset)(-(sm|md|lg|xl))?(-\d+)?$/.test( this ) ) 
					{
						remove.push( this );
					}
				});

				return remove.join( ' ' );
			});

			// Adds classes

			// Merges data (width md is in other param)
			var data = jQuery.extend( 
			{
				width_md : model.data.width || 12
			}, model.data.responsiveness );

			if ( data.offset_md ) 
			{
				$widget.addClass( 'pb-offset-md-' + data.offset_md );
			}

			if ( data.width_md ) 
			{
				$widget.addClass( 'pb-col-md-' + data.width_md );
			}

			else
			{
				$widget.addClass( 'pb-col-md-12' );
			}
		}
	});

	pb.widgets.column = new column();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	pb.control = Class.extend(
	{
		id : null,

		init : function( id )
		{
			this.id = id;

			pb.addAction( 'controlClick/type=' + this.id, this.click );
		},

		click : function( $control, $widget )
		{
			
		}
	});

	pb.controls = {};

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var add = pb.control.extend( 
	{
		init : function()
		{
			this._super( 'add' );
		},

		click : function( $control, $widget )
		{
			pb.widgetPicker( function( $choosen )
			{
				$widget.find( '> .pb-widget-inside > .pb-widget-container' ).append( $choosen );

				pb.doWidgetAction( 'widgetAdd', $choosen );
				pb.loadWidgetPreview( $choosen );

			}, $widget );
		}
	});

	pb.controls.add = new add();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var edit = pb.control.extend( 
	{
		init : function()
		{
			this._super( 'edit' );
		},

		click : function( $control, $widget )
		{
			pb.widgetSettings( $widget );
		}
	});

	pb.controls.edit = new edit();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var copy = pb.control.extend( 
	{
		init : function()
		{
			this._super( 'copy' );
		},

		click : function( $control, $widget )
		{
			var $duplicate = pb.duplicateWidget( $widget );
		
			$duplicate.insertAfter( $widget );
		}
	});

	pb.controls.copy = new copy();

})( jQuery );
(function( $ )
{
	"use strict";

	var pb = window.pb || {};

	var del = pb.control.extend( 
	{
		init : function()
		{
			this._super( 'delete' );
		},

		click : function( $control, $widget )
		{
			var message = pb.get( 'confirmDelete' );

			if ( message && ! window.confirm( message ) ) 
			{
				return;
			}

			$widget.find( '> .pb-widget-inside' ).slideUp( 'fast', function()
			{
				$widget.fadeOut( 'fast', function()
				{
					pb.removeWidget( $widget );
				});
			});
		}
	});

	pb.controls.delete = new del();

})( jQuery );