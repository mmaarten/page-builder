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
