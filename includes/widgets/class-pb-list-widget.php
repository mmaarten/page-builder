<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_List_Widget extends PB_Widget
{
	public function __construct()
	{
		parent::__construct( 'list', __( 'List' ), array
		(
			'description' => __( 'Displays a list.' ),
			'features'    => array( 'id', 'class', 'margin_top', 'margin_bottom' )
		));

		/**
		 * General
		 * -----------------------------------------------------------
		 */

		$this->add_field( array
		(
			'key'           => 'items',
			'name'          => 'items',
			'title'         => __( 'Items' ),
			'description'   => '',
			'type'          => 'repeater',
			'default_value' => '',
			'preview'       => false,
			'order'         => PB_ORDER_TAB_GENERAL + 10
		));

		$this->add_field( array
		(
			'key'           => 'item_text',
			'name'          => 'text',
			'title'         => __( 'Text' ),
			'description'   => '',
			'type'          => 'editor',
			'tinymce'       => array
			(
				'toolbar1' => 'bold,italic,underline,undo,redo,link,fullscreen',
			    'toolbar2' => '',
			    'toolbar3' => '',
			    'toolbar4' => ''
			),
			'default_value' => '',
			'order'         => 10,
			'parent'        => 'items'
		));

		/**
		 * Layout
		 * -----------------------------------------------------------
		 */
		
		$this->add_field( array
		(
			'key'           => 'icon',
			'name'          => 'icon',
			'title'         => __( 'Icon' ),
			'description'   => '',
			'type'          => 'icon',
			'default_value' => '',
			'preview'       => false,
			'order'         => PB_ORDER_TAB_LAYOUT + 10
		));

		$this->add_field( array
		(
			'key'           => 'icon_color',
			'name'          => 'icon_color',
			'title'         => __( 'Icon Color' ),
			'description'   => '',
			'type'          => 'select',
			'choices'       => array_merge( array
			(
				'' => PB_THEME_DEFAULTS,
			), pb()->options->get( 'theme_colors' ) ),
			'default_value' => '',
			'order'         => PB_ORDER_TAB_LAYOUT + 20
		));
	}

	public function widget( $args, $instance )
	{
		$instance = wp_parse_args( $instance, $this->get_defaults() );

		if ( ! $instance['items'] ) 
		{
			return;
		}

		$icon_html = pb()->icons->get_icon_html( $instance['icon'] );

		/**
		 * Output
		 * -----------------------------------------------------------
		 */

		echo $args['before_widget'];

		echo '<ul class="list-unstyled">';

		foreach ( $instance['items'] as $item ) 
		{
			echo '<li>';

			if ( $icon_html ) 
			{
				$icon = array
				(
					'class' => 'pb-list-icon'
				);

				if ( $instance['icon_color'] ) 
				{
					$icon['class'] .= " text-{$instance['icon_color']}";
				}

				printf( '<span%s>%s</span>', pb_render_attributes( $icon ), $icon_html );
			}

			echo apply_filters( 'the_content', $item['text'] );

			echo '</li>';
		}

		echo '</ul>';
		
		echo $args['after_widget'];
	}

	public function preview( $instance )
	{
		$this->preview_meta( $instance );

		if ( ! $instance['items'] ) 
		{
			echo '<div class="pb-widget-preview-content">';

			pb_notice( __( 'No items set.' ), 'info' );

			echo '</div>';

			return;
		}

		echo '<div class="pb-widget-preview-content">';

		$icon_html = pb()->icons->get_icon_html( $instance['icon'] );

		echo '<ul>';

		foreach ( $instance['items'] as $item ) 
		{
			echo '<li>';

			if ( $icon_html ) 
			{
				$icon = array
				(
					'class' => 'pb-list-icon'
				);

				printf( '<span%s>%s</span>', pb_render_attributes( $icon ), $icon_html );
			}

			echo wpautop( $item['text'] );

			echo '</li>';
		}

		echo '</ul>';

		echo '</ul>';
	}
}

pb()->widgets->register( 'PB_List_Widget' );
