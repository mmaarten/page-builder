<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Group_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'group' );
	}

	public function field( $field )
	{
		$field['description_placement'] = 'label';
		
		$sub_fields = pb()->fields->get_sub_fields( $field );

		// Sets default value

		$field['default_value'] = array();

		foreach ( $sub_fields as $sub_field ) 
		{
			$field['default_value'][ $sub_field['name'] ] = $sub_field['default_value'];
		}

		//

		return $field;
	}

	public function render( $field )
	{
		$this->render_block_layout( $field );
	}

	public function render_block_layout( $field )
	{
		$sub_fields = pb()->fields->get_sub_fields( $field );

		if ( ! count( $sub_fields ) ) 
		{
			return;
		}

		usort( $sub_fields, 'pb_sort_order' );

		?>
		
		<div class="pb-sub-fields">
			<?php foreach ( $sub_fields as $sub_field ) :
				
				// Makes field name hierarchical
				$sub_field['prefix'] = $field['name'];
				
				// Sets field value
				$sub_field['value'] = isset( $field['value'][ $sub_field['name'] ] ) ? $field['value'][ $sub_field['name'] ] : $sub_field['default_value'];

			?>
			<?php pb()->fields->render_field( $sub_field ); ?>
			<?php endforeach; ?>
		</div>

		<?php
	}

	public function sanitize( $value, $field )
	{
		$sub_fields = pb()->fields->get_sub_fields( $field );

		if ( ! is_array( $value ) ) 
		{
			$value = array();
		}

		$sanitized = array();

		foreach ( $sub_fields as $sub_field ) 
		{
			$_value = isset( $value[ $sub_field['name'] ] ) ? $value[ $sub_field['name'] ] : $sub_field['default_value'];

			$sanitized[ $sub_field['name'] ] = pb()->fields->sanitize_field( $sub_field, $_value );
		}

		return $sanitized;
	}
}

pb()->field_types->register( 'PB_Group_Field' );
