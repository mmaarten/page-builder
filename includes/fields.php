<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Fields
{
	protected $fields = array();

	public function __construct()
	{
		add_action( 'wp_ajax_pb_settings_update', array( $this, 'update' ) );
	}

	/**
	 * Get Defaults
	 *
	 * Returns a list of field default values.
	 *
	 * @param $page String
	 *
	 * @return mixed array|null
	 */
	public function get_defaults( $page )
	{
		if ( ! isset( $this->fields[ $page ] ) ) 
		{
			return null;
		}

		$fields = wp_filter_object_list( $this->fields[ $page ], array( 'parent' => '' ) );

		$defaults = array();

		foreach ( $fields as $field ) 
		{
			$defaults[ $field['name'] ] = $field['default_value'];
		}

		return $defaults;
	}

	public function get_fields( $page, $search = array()  )
	{
		if ( isset( $this->fields[ $page ] ) ) 
		{
			return wp_filter_object_list( $this->fields[ $page ], $search );
		}

		return null;
	}

	public function get_sub_fields( $field )
	{
		return $this->get_fields( $field['page'], array( 'parent' => $field['key'] ) );
	}

	public function create_field( $args )
	{
		$defaults = array
		(
			'key'                   => '',
			'prefix'                => '',
			'name'                  => '',
			'title'                 => '',
			'description'           => '',
			'description_placement' => 'field', // label|field
			'type'                  => '',
			'default_value'         => '',
			'page'                  => '',
			'parent'				=> '',
			'order'                 => 10,
			'wrapper'               => array
			(
				'id'    => '',
				'class' => '',
				'width' => ''
			)
		);

		$field = wp_parse_args( $args, $defaults );

		// Makes sure all wrapper properties are set

		$field['wrapper'] = wp_parse_args( $field['wrapper'], $defaults['wrapper'] );

		// Possibility to alter field

		$field = apply_filters( "pb/field", $field );
		$field = apply_filters( "pb/field/page={$field['page']}", $field );
		$field = apply_filters( "pb/field/type={$field['type']}", $field );
		$field = apply_filters( "pb/field/key={$field['key']}", $field );

		//

		return $field;
	}

	public function add_field( $field )
	{
		$field = $this->create_field( $field );

		$this->fields[ $field['page'] ][ $field['key'] ] = $field;
	}

	public function render_fields( $page )
	{
		if ( ! isset( $this->fields[ $page ] ) )
		{
			return;
		}

		$fields = wp_filter_object_list( $this->fields[ $page ], array( 'parent' => '' ) );

		if ( ! $fields ) 
		{
			return;
		}

		// Sorts fields on order parameter

		uasort( $fields, 'pb_sort_order' );

		?>

		<div class="pb-fields">
			<?php foreach ( $fields as $field ) : ?>
			<?php $this->render_field( $field ); ?>
			<?php endforeach; ?>
		</div>

		<?php
	}

	public function render_field( $field, $elem = 'div' )
	{
		$field = $this->create_field( $field );

		// Prepares field for input

		$field = $this->prepare_field( $field );

		// Wrapper

		$wrapper = array
		(
			'class'     => 'pb-field',
			'data-key'  => $field['key'],
			'data-page' => $field['page'],
			'data-type' => $field['type']
		);

		if ( $field['key'] ) 
		{
			$wrapper['class'] .= " pb-field-{$field['key']}";
		}

		if ( $field['type'] ) 
		{
			$wrapper['class'] .= " pb-field-type-{$field['type']}";
		}

		if ( $field['wrapper']['id'] ) 
		{
			$wrapper['id'] = $field['wrapper']['id'];
		}

		if ( $field['wrapper']['class'] ) 
		{
			$wrapper['class'] .= " {$field['wrapper']['class']}";
		}

		if ( $field['wrapper']['width'] ) 
		{
			$wrapper['style'] = sprintf( 'width:%s%%;', $field['wrapper']['width'] );
		}

		$wrapper = array_filter( $wrapper );

		// Elements

		$elements = array
		(
			'div' => 'div',
			'ul'  => 'li',
			'ol'  => 'li',
			'tr'  => 'td',
			'td'  => 'div'
		);

		$elem_1 = isset( $elements[ $elem ] ) ? $elem : 'div';
		$elem_2 = $elements[ $elem ];

		// Output

		?>

		<<?php echo $elem_1 . pb_render_attributes( $wrapper ); ?>>

			<<?php echo $elem_2 ?> class="pb-input">

			<?php if ( $field['title'] ) : ?>
			<label class="pb-input-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label><br>
			<?php endif; ?>

			<?php $this->render_field_description( $field, 'label' ); ?>

			<?php do_action( 'pb/render_field/type=' . $field['type'], $field ); ?>

			<?php $this->render_field_description( $field, 'field' ); ?>

			</<?php echo $elem_2 ?>>

		</<?php echo $elem_1 ?>>

		<?php
	}

	public function render_field_description( $field, $context = 'label' )
	{
		if ( ! $field['description'] || $field['description_placement'] != $context ) 
		{
			return;
		}

		printf( '<p class="description">%s</p>', $field['description'] );
	}

	public function prepare_field( $field )
	{
		// Stops when already prepared

		if ( ! empty( $field['_prepared'] ) ) 
		{
			return;
		}

		// Sets field value. TODO : not reliable

		if ( ! isset( $field['value'] ) && ! $field['prefix'] ) 
		{
			$field['value'] = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : $field['default_value'];
		}

		// Sanitizes field value

		pb_stripcslashes( $field['value'] );

		// Makes field name hierarchical

		if ( $field['prefix'] ) 
		{
			$field['name'] = $field['prefix'] . '[' . $field['name'] . ']';
		}

		// Generates id from field name

		$field['id'] = 'pb-input-' . str_replace( array( '][', '[', ']' ), array( '-', '-', '' ), $field['name'] );

		// Notifies observers
		
		$field = apply_filters( "pb/prepare_field", $field );
		$field = apply_filters( "pb/prepare_field/page={$field['page']}", $field );
		$field = apply_filters( "pb/prepare_field/type={$field['type']}", $field );
		$field = apply_filters( "pb/prepare_field/key={$field['key']}", $field );

		// Marks field as prepared

		$field['_prepared'] = true;

		//

		return $field;
	}

	public function sanitize_field( $field, $value )
	{
		$value = apply_filters( "pb/sanitize_field", $value, $field );
		$value = apply_filters( "pb/sanitize_field/page={$field['page']}", $value, $field );
		$value = apply_filters( "pb/sanitize_field/type={$field['type']}", $value, $field );
		$value = apply_filters( "pb/sanitize_field/key={$field['key']}"  , $value, $field );

		return $value;
	}

	public function settings_fields( $page )
	{
		wp_nonce_field( 'settings_fields', PB_NONCE_NAME );

		echo '<input type="hidden" name="action" value="pb_settings_update">';
		printf( '<input type="hidden" name="option_page" value="%s">', esc_attr( $page ) );
	}

	public function update()
	{
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) 
		{
			return;
		}

		check_admin_referer( 'settings_fields', PB_NONCE_NAME );

		$page = isset( $_POST['option_page'] ) ? $_POST['option_page'] : '';

		$sanitized = array();

		if ( isset( $this->fields[ $page ] ) ) 
		{
			foreach ( $this->fields[ $page ] as $field ) 
			{
				$name = $field['name'];
				$value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';

				$value = $this->sanitize_field( $field, $value );

				$sanitized[ $name ] = $value;
			}
		}

		wp_send_json( $sanitized );
	}
}

pb()->fields = new PB_Fields();




