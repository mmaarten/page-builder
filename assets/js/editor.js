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

	var pb = {};

	window.pb = pb;

})( jQuery, window, undefined );

/**
 * Hooks
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	pb.hooks = 
	{
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
	};

})( jQuery, window, undefined );

/**
 * Fields
 */
(function( $, window, undefined )
{
	"use strict";

	var pb = window.pb || {};

	pb.field = 
	{
		id : null,

		extend : function( options )
		{
			$.extend( this, options );

			pb.hooks.addAction( 'field/type=' + this.id, this.field );
		},

		field : function( $field )
		{
			
		},
	};

	pb.fields = {};

	$( document ).on( 'ready', function()
	{
		$( '.pb-fields > .pb-field' ).each( function()
		{
			var $field = $( this );

			pb.hooks.doAction( 'field', $field );
			pb.hooks.doAction( 'field/type=' + $field.data( 'type' ), $field );
			pb.hooks.doAction( 'field/key=' + $field.data( 'key' ), $field );
		});
	});

})( jQuery, window, undefined );

/**
 * Image Field
 */
(function( $, window, undefined )
{
	"use strict";

	var image = pb.field.extend(
	{
		id : 'image',

		field : function( $field )
		{
			console.log( $field.get(0) );
		},
	});

	pb.fields.image = image;

})( jQuery, window, undefined );

/**
 * WYSIWYG Field
 */
(function( $, window, undefined )
{
	"use strict";

	var wysiwyg = pb.field.extend(
	{
		id : 'wysiwyg',

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

			var fieldOptions = {};

			// mce init

			var init = $.extend( {}, defaults, fieldOptions );

			init.id = newId;
			init.selector = '#' + newId;

			// Stores settings
			tinyMCEPreInit.mceInit[ newId ] = init;

			tinymce.init( init );
		},
	});

	pb.fields.wysiwyg = wysiwyg;

})( jQuery, window, undefined );
