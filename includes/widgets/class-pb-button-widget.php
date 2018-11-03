<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Button_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'button', __( 'Button' ), array
		(
			'description' => __( 'Displays a button.' ),
			'features'    => array( 'id', 'class', 'mt', 'mr', 'mb', 'ml' ),
		));

		// General

		$this->add_field( array
		(
			'key'           => "{$this->id}_text",
			'name'          => 'text',
			'label'         => __( 'Text' ),
			'description'   => '',
			'type'          => 'text',
			'default_value' => __( 'Button' ),
			'preview'       => true,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_link",
			'name'          => 'link',
			'label'         => __( 'Link' ),
			'description'   => '',
			'type'          => 'url',
			'default_value' => '',
			'preview'       => true,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_link_tab",
			'name'          => 'link_tab',
			'label'         => __( 'Open link in new window.' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_toggle",
			'name'          => 'toggle',
			'label'         => __( 'Toggle' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				''         => __( "- Don't toggle -" ),
				'modal'    => __( 'Modal' ),
				'collapse' => __( 'Collapse' ),
			),
			'default_value' => '',
		));

		// Layout

		$this->add_field( array
		(
			'key'           => "{$this->id}_type",
			'name'          => 'type',
			'label'         => __( 'Type' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'primary'   => __( 'Primary' ),
				'secondary' => __( 'Secondary' ),
				'success'   => __( 'Success' ),
				'danger'    => __( 'Danger' ),
				'warning'   => __( 'Warning' ),
				'info'      => __( 'Info' ),
				'light'     => __( 'Light' ),
				'dark'      => __( 'Dark' ),
				'link'      => __( 'Link' ),
			),
			'default_value' => 'primary',
			'category'      => 'layout',
			'preview'       => true,
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_outline",
			'name'          => 'outline',
			'label'         => __( 'Outline' ),
			'description'   => '',
			'type'          => 'true_false',
			'default_value' => 0,
			'category'      => 'layout',
		));

		$this->add_field( array
		(
			'key'           => "{$this->id}_size",
			'name'          => 'size',
			'label'         => __( 'Size' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array
			(
				'sm' => __( 'Small' ),
				'md' => __( 'Medium' ),
				'lg' => __( 'Large' ),
			),
			'default_value' => 'md',
			'category'      => 'layout',
		));
	}
}

pb()->widgets->register_widget( 'PB_Button_Widget' );
