<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Widget
{
	public $id          = null;
	public $title       = null;
	public $description = null;

	public function __construct( $id, $title, $args = array() )
	{
		$defaults = array
		(
			'description' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->id          = $id;
		$this->title       = $title;
		$this->description = $args['description'];
	}
}
