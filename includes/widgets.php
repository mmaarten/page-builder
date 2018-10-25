<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Widgets
{
	protected $widgets = array();

	public function __construct()
	{

	}

	public function register_widget( $widget )
	{
		if ( ! $widget instanceof PB_Widget ) 
		{
			$widget = new $widget();
		}

		$this->widgets[ $widget->id ] = $widget;
	}

	public function unregister_widget( $widget_id )
	{
		unset( $this->widgets[ $widget_id ] );
	}

	public function get_widgets()
	{
		return $this->widgets;
	}

	public function get_widget( $widget_id )
	{
		if ( isset( $this->widgets[ $widget_id ] ) ) 
		{
			return $this->widgets[ $widget_id ];
		}

		return null;
	}
}

pb()->widgets = new PB_Widgets();
