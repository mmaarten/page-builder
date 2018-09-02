<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Widgets
{
	public $post  = null;
	public $model = null;

 	protected $widgets = array();

	function __construct()
	{
			
	}

	function register( $widget )
	{
		if ( ! $widget instanceof PB_Widget ) 
		{
			$widget = new $widget();
		}

		$this->widgets[ $widget->id ] = $widget;
	}

	function unregister( $id )
	{
		unset( $this->widgets[ $id ] );
	}

	function get_widgets()
	{
		return $this->widgets;
	}

	function get_widget( $id )
	{
		if ( isset( $this->widgets[ $id ] ) )
		{
			return $this->widgets[ $id ];
		}

		return null;
	}

	public function add_widget_support( $widget_id, $feature )
	{
		$widget = $this->get_widget( $widget_id );

		if ( ! $widget ) 
		{
			return;
		}

		$widget->add_support( $feature );
	}

	public function render_widget( $model )
	{
		// Makes sure all properties are set

		$model = pb()->models->create_model( $model );

		// Gets widget

		$widget = $this->get_widget( $model['type'] );

		// Stops when widget could not be found

		if ( ! $widget )
		{
			return;
		}

		// Wrapper

		$wrapper = array
		(
			'id'    => '',
			'class' => "pb-widget pb-{$widget->id}-widget"
		);

		if ( $model['id'] ) 
		{
			$wrapper['id'] = "pb-widget-{$model['id']}";
		}

		$wrapper = apply_filters( "pb/widget_html_attributes/type={$widget->id}", $wrapper, $model['data'], $widget );
		$wrapper = apply_filters( "pb/widget_html_attributes", $wrapper, $model['data'], $widget );
		$wrapper = array_filter( $wrapper );

		$args = array
		(
			'before_widget' => sprintf( '<div%s>', pb_render_attributes( $wrapper ) ),
			'after_widget'  => '</div>'
		);

		$args = apply_filters( 'pb_widget_args', $args, $model['data'], $widget );

		// Renders widget

		$widget->widget( $args, $model['data'] );
	}

	public function render_post_widgets( $post = 0, $search = null )
	{
		$this->post  = null;
		$this->model = null;

		$post = get_post( $post );

		if ( ! $post ) 
		{
			return;
		}

		$models = pb()->models->get_post_models( $post->ID );

		if ( ! $models ) 
		{
			return;
		}

		$this->post = $post;

		if ( ! isset( $search ) ) 
		{
			$search = array( 'parent' => '' );
		}

		$models = wp_filter_object_list( $models, $search );

		foreach ( $models as $model ) 
		{
			$this->model = $model;

			$this->render_widget( $this->model );
		}
	}

	public function the_child_widgets()
	{
		if ( ! $this->post || ! $this->model ) 
		{
			return;
		}

		$this->render_post_widgets( $this->post, array( 'parent' => $this->model['id'] ) );
	}

	public function post_has_widget( $post_id, $widget_id )
	{
		$models = pb()->models->get_post_models( $post_id );

		if ( $models )
		{
			foreach ( $models as $model ) 
			{
				if ( $model['type'] == $widget_id ) 
				{
					return true;
				}
			}
		}

		return false;
	}

	public function post_has_widgets( $post_id = 0 )
	{
		$models = pb()->models->get_post_models( $post_id );

		return $models && count( $models ) ? true : false;
	}

	public function enqueue_widget_scripts( $widget_id )
	{
		$widget = $this->get_widget( $widget_id );

		if ( ! $widget ) 
		{
			return;
		}

		$widget->enqueue_scripts();
	}

	public function list_editor_widgets( $args = array() )
	{
		$defaults = array
		(
			'before'        => '',
			'before_widget' => '',
			'after_widget'  => '',
			'after'         => '',
			'include'       => null
		);

		$args = wp_parse_args( $args, $defaults );

		// Filter

		$widgets = $this->widgets;

		if ( $args['include'] )
		{
			$widgets = array_intersect_key( $widgets, array_flip( (array) $args['include'] ) );
		}

		//

		if ( ! count( $widgets ) ) 
		{
			return;
		}

		echo $args['before'];

		foreach ( $widgets as $widget ) 
		{
			echo $args['before_widget'];

			$widget->editor_widget();

			echo $args['after_widget'];
		}

		echo $args['after'];
	}
}

pb()->widgets = new PB_Widgets();

function pb_auto_render_post_widgets( $content )
{
	remove_filter( current_filter(), __FUNCTION__ );

	ob_start();

	pb()->widgets->render_post_widgets();

	$content .= ob_get_clean();

	add_filter( current_filter(), __FUNCTION__ );

	return $content;
}

add_filter( 'the_content', 'pb_auto_render_post_widgets' );
