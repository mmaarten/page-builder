<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Widgets
{
	protected $widgets = array();

	public function __construct()
	{
		
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
}

pb()->widgets = new PB_Widgets();
