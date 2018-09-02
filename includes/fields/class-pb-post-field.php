<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Post_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'post' );

		add_action( 'wp_ajax_pb_post_field_get_choices', array( $this, 'get_choices' ) );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'multiple'  => false,
			'post_type' => 'post'
		);

		$field = wp_parse_args( $field, $defaults );

		if ( $field['multiple'] ) 
		{
			$field['default_value'] = (array) $field['default_value'];
		}

		elseif ( is_array( $field['default_value'] ) || is_object( $field['default_value'] ) )
		{
			$field['default_value'] = '';
		}

		if ( ! $field['post_type'] || ! post_type_exists( $field['post_type'] ) ) 
		{
			$field['post_type'] = $defaults['post_type'];
		}

		return $field;
	}

	public function render( $field )
	{
		/**
		 * Choices
		 * -----------------------------------------------------------
		 */

		$selected = $field['value'] ? (array) $field['value'] : array();

		// Sets selected choices only.
		// other choices are filled in when searching

		$choices = array();

		if ( count( $selected ) ) 
		{
			$posts = get_posts( array
			(
				'post_type'   => $field['post_type'],
				'post__in'    => $selected,
				'orderby'     => 'post__in',
				'order'       => 'ASC',
				'numberposts' => count( $selected )
			));

			foreach ( $posts as $post ) 
			{
				$choices[ $post->ID ] = $post->post_title;
			}
		}

		// Attributes

		$atts = array
		(
			'id'    => $field['id'],
			'name'  => $field['multiple'] ? "{$field['name']}[]" : $field['name']
		);

		if ( $field['multiple'] ) 
		{
			$atts['multiple'] = 'multiple';
		}

		$atts = array_filter( $atts );

		// Output

		printf( '<select%s>', pb_render_attributes( $atts ) );

		pb_dropdown_options( $choices, $selected );

		echo '</select>';
	}

	public function sanitize( $value, $field )
	{
		$sanitized = array();

		foreach ( (array) $value as $post_id ) 
		{
			if ( $post_id && get_post_type( $post_id ) == $field['post_type'] ) 
			{
				$sanitized[] = intval( $post_id );
			}
		}

		if ( ! empty( $field['multiple'] ) ) 
		{
			return $sanitized;
		}

		if ( count( $sanitized ) ) 
		{
			return $sanitized[0];
		}

		return '';
	}

	public function translate( $value, $field )
	{
		$values = $value ? (array) $value : array();

		$translated = array();

		if ( count( $values ) ) 
		{
			$posts = get_posts( array
			(
				'post_type'   => $field['post_type'],
				'post__in'    => $values,
				'orderby'     => 'post__in',
				'order'       => 'ASC',
				'numberposts' => count( $values )
			));

			foreach ( $posts as $post ) 
			{
				$translated[] = $post->post_title;
			}
		}

		return implode( ', ', $translated );
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'select2' );
	}

	public function get_choices()
	{
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		$search     = isset( $_POST['search'] ) ? $_POST['search'] : '';
		$field_key  = isset( $_POST['field'] ) ? $_POST['field'] : '';
		$field_page = isset( $_POST['page'] ) ? $_POST['page'] : '';
		$paged      = isset( $_POST['paged'] ) ? $_POST['paged'] : 1;

		$fields = pb()->fields->get_fields( $field_page );

		$field = $fields[ $field_key ];

		$the_query = new WP_Query( array
		(
			's'           => $search,
			'post_type'   => $field['post_type'],
			'paged'       => $paged,
			'orderby'     => 'post_title',
			'order'       => 'ASC',
			'numberposts' => PB_MAX_NUMBERPOSTS
		));

		$posts = $the_query->posts;

		if ( count( $posts ) && is_post_type_hierarchical( $the_query->get( 'post_type' ) ) )
		{
			$post_ids = wp_filter_object_list( $posts, array(), 'and', 'ID' );

			$posts = get_pages( array
			(
				'include'      => $post_ids,
				'post_type'    => $the_query->get( 'post_type' ),
				'hierarchical' => true,
				'sort_column'  => 'menu_order',
				'sort_order'   => 'ASC',
				'number'       => count( $post_ids )
			));
		}

		$items = array();

		foreach ( $posts as $post ) 
		{
			$text = $post->post_title;

			if ( is_post_type_hierarchical( $the_query->get( 'post_type' ) ) )
			{
				$ancestors = get_post_ancestors( $post );

				$text = str_repeat( '—', count( $ancestors ) ) . " {$post->post_title}";
			}

			else
			{
				$text = $post->post_title;
			}

			$items[] = array
			(
				'id'   => $post->ID,
				'text' => $text
			);
		}

		wp_send_json( array
		(
			'items'         => $items,
			'paged'         => $the_query->get( 'paged' ),
			'max_num_pages' => $the_query->max_num_pages
		));
	}
}

pb()->field_types->register( 'PB_Post_Field' );

