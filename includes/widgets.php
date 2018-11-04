<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Widgets
{
	protected $widgets = array();

	protected $post  = null;
	protected $model = null;

	public function __construct()
	{
		add_filter( 'the_content', array( $this, 'auto_render_post_widgets' ) );
	}

	public function register_widget( $widget )
	{
		if ( ! $widget instanceof PB_Field ) 
		{
			$widget = new $widget();
		}

		$this->widgets[ $widget->id ] = $widget;
	}

	public function unregister_widget( $id )
	{
		unset( $this->widgets[ $id ] );
	}

	public function get_widgets()
	{
		return $this->widgets;
	}

	public function get_widget( $id )
	{
		if ( isset( $this->widgets[ $id ] ) ) 
		{
			return $this->widgets[ $id ];
		}

		return null;
	}

	public function get_models( $post_id )
	{
		if ( metadata_exists( 'post', $post_id, 'pb_models' ) ) 
		{
			return get_post_meta( $post_id, 'pb_models', true );
		}

		return null;
	}

	public function has_widgets( $post_id = 0 )
	{
		$post = get_post( $post_id );

		if ( ! $post ) 
		{
			return false;
		}

		if ( ! post_type_supports( $post->post_type, PB_POST_TYPE_FEATURE ) ) 
		{
			return false;
		}

		$models = $this->get_models( $post->ID );

		if ( ! $models ) 
		{
			return false;
		}

		return true;
	}

	public function the_widgets( $_search = null )
	{
		$this->post = get_post();

		if ( ! $this->post ) 
		{
			return;
		}

		if ( ! $this->has_widgets( $this->post ) ) 
		{
			return;
		}

		$models = $this->get_models( $this->post->ID );

		if ( ! $_search ) 
		{
			$_search = array( 'parent' => '' );
		}

		$models = wp_filter_object_list( $models, $_search );

		foreach ( $models as $model ) 
		{
			$this->model = $model;

			$this->render_widget( $this->model['type'], $this->model['data'] );
		}
	}

	public function the_child_widgets()
	{
		if ( ! $this->post || ! $this->model ) 
		{
			return;
		}

		$this->the_widgets( array( 'parent' => $this->model['id'] ) );
	}

	public function auto_render_post_widgets( $content )
	{
		remove_filter( current_filter(), array( $this, __FUNCTION__ ) );

		if ( $this->has_widgets() ) 
		{
			ob_start();

			pb()->widgets->the_widgets();

			$content .= ob_get_clean();
		}

		add_filter( current_filter(), array( $this, __FUNCTION__ ) );

		return $content;
	}

	public function enqueue_widgets_scripts()
	{
		$widgets = $this->get_widgets();

		foreach ( $widgets as $widget ) 
		{
			$this->enqueue_widget_scripts( $widget->id );
		}
	}

	public function enqueue_widget_scripts( $id )
	{
		static $enqueued = array();
		
		$widget = $this->get_widget( $id );

		if ( ! $widget || isset( $enqueued[ $widget->id ] ) ) 
		{
			return;
		}

		$widget->enqueue_scripts();

		$enqueued[ $widget->id ] = true;
	}

	public function render_widget( $id, $instance )
	{
		$widget = $this->get_widget( $id );

		if ( ! $widget ) 
		{
			return;
		}

		// Wrapper

		$wrapper = array
		(
			'class' => "pb-widget pb-{$widget->id}-widget",
		);

		$wrapper = apply_filters( 'pb/widget_html_attributes'                   , $wrapper, $widget, $instance );
		$wrapper = apply_filters( "pb/widget_html_attributes/type={$widget->id}", $wrapper, $widget, $instance );
		$wrapper = array_filter( $wrapper );

		// Args
		
		$args = array
		(
			'before' => '<div' . pb_esc_attr( $wrapper ) . '>',
			'after'  => '</div>',
		);

		// Output

		$widget->render( $args, $instance );
	}
}

pb()->widgets = new PB_Widgets();
