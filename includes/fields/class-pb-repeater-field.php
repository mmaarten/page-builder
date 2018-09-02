<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Repeater_Field extends PB_Field
{
	protected $layouts = array();

	public function __construct()
	{
		parent::__construct( 'repeater' );
	}

	public function render( $field )
	{
		$defaults = array
		(
			'value'        => array(),
			'add_row_text' => __( 'Add Row' )
		);

		$field = wp_parse_args( $field, $defaults );

		$sub_fields = pb()->fields->get_sub_fields( $field );

		// Stops when no sub fields.

		if ( ! $sub_fields ) 
		{
			return;
		}

		$value = is_array( $field['value'] ) ? $field['value'] : array();

		// Adds row with no values for clone.

		$clone_index = 0;

		array_splice( $value, $clone_index, 0, array( array() ) );
		
		?>

		<div class="pb-repeater">

			<table class="pb-repeater-table">

				<thead>
					<tr>
						<th class="move"></th>
						<?php foreach ( $sub_fields as $sub_field ) : ?>
						<th>
							<?php if ( $sub_field['title'] ) : ?>
							<strong><?php echo esc_html( $sub_field['title'] ); ?></strong>
							<?php endif; ?>
							
							<?php if ( $sub_field['description'] ) : ?>
							<p class="description"><?php echo $sub_field['description']; ?></p>			
							<?php endif; ?>
						</th>
						<?php endforeach; ?>
						<th class="remove"></th>
					</tr>
				</thead>

				<tbody class="pb-repeater-rows">

					<?php foreach ( $value as $i => $row ) : ?>
					<tr class="pb-repeater-row<?php echo $i == $clone_index ? ' pb-clone' : ''; ?>">
						<th class="move"><span class="dashicons dashicons-move"></span></th>
						<?php foreach ( $sub_fields as $sub_field ) : 

							// Title and description are displayed in header

							$sub_field['title']       = '';
							$sub_field['description'] = '';

							// Sets input value

							if ( isset( $row[ $sub_field['name'] ] ) ) 
							{
								$sub_field['value'] = $row[ $sub_field['name'] ];
							} 

							else
							{
								$sub_field['value'] = $sub_field['default_value'];	
							}
							
							// Makes input name hierarchical
							
							$sub_field['prefix'] = $field['name'] . '[' . $i . ']';

						?>
						<?php pb()->fields->render_field( $sub_field, 'td' ); ?>
						<?php endforeach; ?>
						<th class="remove"><button type="button" class="button-link pb-repeater-remove dashicons-before dashicons-trash"></button></th>
					</tr>
					<?php endforeach; ?>
					
				</tbody>
				
			</table>

			<p class="pb-repeater-footer">
				<button type="button" class="button pb-repeater-add"><?php echo esc_html( $field['add_row_text'] ); ?></button>
			</p>

		</div><!-- .pb-repeater -->

		<?php
	}

	public function sanitize( $value, $field )
	{
		$sub_fields = pb()->fields->get_sub_fields( $field );

		if ( ! $sub_fields ) 
		{
			return array();
		}

		$value = is_array( $value ) ? $value : array();

		// Removes row for cloning
		unset( $value[0] );

		// Restores indexes
		$value = array_values( $value );

		$sanitized = array();

		foreach ( $value as $i => $row ) 
		{
			foreach ( $sub_fields as $sub_field )
			{
				if ( isset( $row[ $sub_field['name'] ] ) ) 
				{
					$_value = $row[ $sub_field['name'] ];
				}

				else
				{
					$_value = $sub_field['default_value'];
				}

				$sanitized[ $i ][ $sub_field['name'] ] = pb()->fields->sanitize_field( $sub_field, $_value );
			}
		}

		return $sanitized;
	}
}

pb()->field_types->register( 'PB_Repeater_Field' );

