<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_URL_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'url' );

		add_action( 'wp_ajax_pb_url_autocomplete', array( $this, 'get_choices' ) );
	}

	public function field( $field )
	{
		$defaults = array
		(
			'search' => get_post_types( array( 'public' => true ), 'names' )
		);

		$field = wp_parse_args( $field, $defaults );

		$field['search'] = $field['search'] ? (array) $field['search'] : array();

		return $field;
	}

	public function render( $field )
	{
		$is_search = count( $field['search'] ) ? true : false;

		// Attributes

		$atts = array
		(
			'type'        => $is_search ? 'search' : 'text',
			'id'          => $field['id'],
			'name'        => $field['name'],
			'value'       => $field['value'],
			'data-search' => $is_search ? '1' : '0'
		);

		$atts = array_filter( $atts );

		// Output

		printf( '<input%s>', pb_render_attributes( $atts ) );
	}

	public function sanitize( $value, $field )
	{
		return esc_url( $value );
	}

	public function translate( $value, $field )
	{
		return esc_url( $value );
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
	}

	public function get_choices()
	{
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) 
		{
			return;
		}

		check_admin_referer( 'editor', PB_NONCE_NAME );

		$term       = isset( $_POST['term'] ) ? $_POST['term'] : '';
		$field_key  = isset( $_POST['field'] ) ? $_POST['field'] : '';
		$field_page = isset( $_POST['page'] ) ? $_POST['page'] : '';

		$fields = pb()->fields->get_fields( $field_page );
		$field = $fields[ $field_key ];

		//

		$the_query = new WP_Query( array
		(
			's'              => $term,
			'post_type'      => $field['search'],
			'posts_per_page' => 50,
			// include 'inherit' for attachments
			'post_status'    => array( 'publish', 'inherit' )
		));

		$data = array();

		foreach ( $the_query->posts as $post ) 
		{
			$data[] = array
			(
				'value' => get_permalink( $post->ID ),
				'label' => get_the_title( $post->ID ),
				'url'   => substr( get_permalink( $post->ID ), strlen( site_url() ) )
			);
		}

		wp_send_json( $data );
	}
}

pb()->field_types->register( 'PB_URL_Field' );