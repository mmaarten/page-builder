<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Term_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'term' );

		add_action( 'wp_ajax_pb_term_field_get_choices', array( $this, 'get_choices' ) );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'multiple' => false,
			'taxonomy' => 'category'
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

		if ( ! $field['taxonomy'] || ! taxonomy_exists( $field['taxonomy'] ) ) 
		{
			$field['taxonomy'] = $defaults['taxonomy'];
		}

		return $field;
	}

	public function render( $field )
	{
		// Choices

		$selected = $field['value'] ? (array) $field['value'] : array();

		$choices = array();

		if ( count( $selected ) ) 
		{
			$terms = get_terms( array
			(
				'taxonomy' => $field['taxonomy'],
				'include'  => $selected,
				'orderby'  => 'include',
				'order'    => 'ASC',
				'number'   => count( $selected )
			));

			foreach ( $terms as $term ) 
			{
				$choices[ $term->term_id ] = $term->name;
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

		foreach ( (array) $value as $term_id ) 
		{
			if ( $term_id && get_term( $term_id ) ) 
			{
				$sanitized[] = intval( $term_id );
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
			$terms = get_terms( array
			(
				'taxonomy' => $field['taxonomy'],
				'include'  => $values,
				'orderby'  => 'include',
				'order'    => 'ASC',
				'number'   => count( $values )
			));

			foreach ( $terms as $term ) 
			{
				$translated[] = $term->name;
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

		$per_page = 10;

		$fields = pb()->fields->get_fields( $field_page );

		$field = $fields[ $field_key ];

		$total = wp_count_terms( $field['taxonomy'] );

		$terms = get_terms( array
		(
			'taxonomy'   => $field['taxonomy'],
			'orderby'    => 'name',
			'order'      => 'ASC',
			'name__like' => $search,
			'offset'     => ( $paged - 1 ) * $per_page,
			'number'     => $per_page
		));

		$items = array();

		foreach ( $terms as $term ) 
		{
			$items[] = array
			(
				'id'   => $term->term_id,
				'text' => $term->name
			);
		}

		wp_send_json( array
		(
			'items'         => $items,
			'paged'         => $paged,
			'max_num_pages' => ceil( ( $total + 1 ) / $per_page  )
		));
	}
}

pb()->field_types->register( 'PB_Term_Field' );

