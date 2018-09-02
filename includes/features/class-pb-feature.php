<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Feature
{
	public $id = null;

	public function __construct( $id )
	{
		$this->id = $id;

		add_action( 'pb/widget'		   		   , array( $this, 'widget_init' ) );
		add_filter( 'pb/widget_html_attributes', array( $this, 'widget_html_attributes' ), 10, 3 );
	}

	public function widget_init( $widget )
	{
		
	}

	public function widget_html_attributes( $atts, $instance, $widget )
	{
		return $atts;
	}
}