<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Group_Field extends PB_Field
{
	public function __construct()
	{
		parent::__construct( 'group' );
	}

	public function field( $field )
	{
		// Make sure sub fields are setup correctly.

		$sub_fields = array();

		if ( isset( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) 
		{
			foreach ( $field['sub_fields'] as $sub_field ) 
			{
				$sub_field = pb()->fields->create_field( $sub_field );

				$sub_field['key'] = '';

				$sub_fields[] = $sub_field;
			}
		}

		$field['sub_fields'] = $sub_fields;

		// Set defaults

		$defaults = array();

		foreach ( $field['sub_fields'] as $sub_field ) 
		{
			$defaults[ $sub_field['name'] ] = $sub_field['default_value'];
		}

		$field['default_value'] = $defaults;

  		// Description placement
  		
  		$field['description_placement'] = 'label';

		// Return

		return $field;
	}

	public function render( $field )
	{
		?>

		<div class="pb-sub-fields">
			<?php foreach ( $field['sub_fields'] as $sub_field ) : 
				$sub_field['prefix'] = $field['name'];
				$sub_field['value']  = $field['value'][ $sub_field['name'] ];
			?>
			<?php pb()->fields->render_field( $sub_field ); ?>
			<?php endforeach; ?>
		</div>

		<?php
	}

	public function sanitize( $value, $field )
	{
		$values = array();

		foreach ( $field['sub_fields'] as $sub_field ) 
		{
			$_value = $sub_field['default_value'];

			if ( isset( $value[ $sub_field['name'] ] ) ) 
			{
				$_value = $value[ $sub_field['name'] ];
			}

			$values[ $sub_field['name'] ] = apply_filters( "pb/sanitize_field/type={$sub_field['type']}", $_value, $sub_field );
		}

		return $values;
	}

	public function translate( $value, $field )
	{
		return '';
	}
}

pb()->field_types->register_field( 'PB_Group_Field' );
