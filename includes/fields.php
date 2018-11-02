<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Fields
{
	protected $fields = array();

	public function __construct()
	{
		add_action( 'wp_ajax_pb_fields_update', array( $this, 'update' ) );
	}

	public function get_fields( $search = null )
	{
		return wp_filter_object_list( $this->fields, $search );
	}

	public function create_field( $args )
	{
		// Defaults
		$defaults = array
		(
			'key'                   => '',
			'prefix'                => 'pb_input',
			'name'                  => '',
			'label'                 => '',
			'description'           => '',
			'description_placement' => 'field',
			'type'                  => '',
			'default_value'         => '',
			'order'                 => 0,
			'group'                 => '',
		);

		// Create
		$field = wp_parse_args( $args, $defaults );

		// Extend
		$field = apply_filters( "pb/field/type={$field['type']}", $field );

		// Return 
		return $field;
	}

	public function add_field( $field )
	{
		// Make sure field is setup correctly.
		$field = $this->create_field( $field );

		// Register
		$this->fields[ $field['key'] ] = $field;
	}

	public function prepare_field( $field )
	{
		// Value
		if ( ! isset( $field['value'] ) ) 
		{
			$field['value'] = apply_filters( "pb/input_value", $field['default_value'], $field );
		}

		// Name
		if ( $field['key'] ) 
		{
			$field['name'] = $field['key'];
		}

		// Make field name hierarchical
		if ( $field['prefix'] ) 
		{
			$field['name'] = $field['prefix'] . '[' . $field['name'] . ']';
		}

		// Generate id based on field name
		$field['id'] = str_replace( array( '[', '][', ']' ), array( '-', '-', '' ), $field['name'] );

		// Extend
		$field = apply_filters( "pb/prepare_field/type={$field['type']}", $field );

		// Return
		return $field;
	}

	public function render_field( $field )
	{
		// Prepare for input
		$field = $this->prepare_field( $field );

		// Wrapper

		$wrapper = array
		(
			'class'     => "pb-field",
			'data-key'  => $field['key'],
			'data-type' => $field['type'],
		);

		if ( $field['key'] ) 
		{
			$wrapper['class'] .= " pb-field-{$field['key']}";
		}

		$wrapper = array_filter( $wrapper );

		?>

		<div<?php echo pb_esc_attr( $wrapper ); ?>>

			<?php if ( $field['label'] ) : ?>
			<label class="pb-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<?php endif; ?>

			<?php $this->render_field_description( $field, 'label' ); ?>

			<?php do_action( "pb/render_field/type={$field['type']}", $field ); ?>

			<?php $this->render_field_description( $field, 'field' ); ?>

		</div>

		<?php
	}

	public function render_field_description( $field, $context )
	{
		if ( ! $field['description'] || $context != $field['description_placement'] ) 
		{
			return;
		}

		echo '<p class="description">' . $field['description'] . '</p>' . "\n";
	}

	public function render_fields( $group )
	{
		$fields = $this->get_fields( array( 'group' => $group ) );

		if ( ! $fields ) 
		{
			return;
		}

		// Sort fields
		usort( $fields, 'pb_sort_order' );

		?>

		<div class="pb-fields">
			<?php foreach ( $fields as $field ) : ?>
			<?php $this->render_field( $field ); ?>
			<?php endforeach; ?>
		</div>

		<?php
	}

	public function settings_fields( $group )
	{
		wp_nonce_field( 'pb_fields_update', PB_NONCE_NAME );

		?>

		<input type="hidden" name="action" value="pb_fields_update">
		<input type="hidden" name="pb_field_group" value="<?php echo esc_attr( $group ); ?>">

		<?php
	}

	public function update()
	{
		// Check ajax and referer

		if ( ! wp_doing_ajax() ) 
		{
			return;
		}
 
		check_admin_referer( 'pb_fields_update', PB_NONCE_NAME );

		// Post data

		$group = $_POST['pb_field_group'];

		// Sanitize user input

		$fields = $this->get_fields( array( 'group' => $group ) );

		$options = array();

		foreach ( $fields as $field ) 
		{
			$base = $field['prefix'] ? $_POST[ $field['prefix'] ] : $_POST;

			$option_name  = $field['name'];
			$option_value = $field['default_value'];

			if ( isset( $base[ $field['key'] ] ) ) 
			{
				$option_value = $base[ $field['key'] ];
			}

			$options[ $option_name ] = apply_filters( "pb/sanitize_field/type={$field['type']}", $option_value, $field );
		}

		// Response

		wp_send_json( $options );
	}
}

pb()->fields = new PB_Fields();
