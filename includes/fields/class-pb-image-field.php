<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exits when accessed directly.

class PB_Image_Field extends PB_Field
{
    public function __construct()
    {
        parent::__construct( 'image' );
    }

    public function field( $field )
    {
        $defaults = array
        (
            'multiple' => false
        );

        return wp_parse_args( $field, $defaults );
    }

    public function render( $field )
    {
        static $counter = 0;

        $counter++;

        $selected = $field['value'] ? (array) $field['value'] : array();
        
        $picker_id = $field['id'] ? "pb-image-picker-{$field['id']}" : "pb-image-picker-$counter";

        $add_text = $field['multiple'] ? __( 'Add Image' ) : __( 'Set Image' );

        ?>

        <div id="<?php echo esc_attr( $picker_id ); ?>" class="pb-image-picker" data-multiple="<?php echo $field['multiple'] ? 1 : ''; ?>">

            <div class="pb-clone">
                <?php $this->render_item( $field ); ?>
            </div>

            <div class="pb-image-picker-items">
                <?php foreach ( $selected as $attachment_id ) : ?>
                <?php $this->render_item( $field, $attachment_id ); ?>
                <?php endforeach; ?>
            </div>

            <p class="pb-image-picker-add">
                <button type="button" class="button pb-image-picker-add-control"><?php echo esc_html( $add_text ); ?></button>
            </p>

        </div><!-- .pb-image-picker -->

        <?php
    }

    public function render_item( $field, $attachment_id = 0 )
    {
        ?>

        <div class="pb-image-picker-item">
            <input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>[]" value="<?php echo esc_attr( $attachment_id ); ?>">
            <img src="<?php echo wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); ?>">
            <button type="button" class="button pb-image-picker-delete-control dashicons-before dashicons-no-alt"><span class="screen-reader-text"><?php esc_html_e( 'Delete' ); ?></span></button>
        </div>

        <?php
    }

    public function sanitize( $value, $field )
    {
        $value = $value ? (array) $value : array();

        // Removes clone

        if ( $value ) 
        {
            unset( $value[0] );
        }

        // Restores indexes

        $value = array_values( $value );

        //

        $sanitized = array();

        foreach ( $value as $attachment_id ) 
        {
            $attachment_id = intval( $attachment_id );

            if ( get_post_type( $attachment_id ) != 'attachment' ) 
            {
                continue;
            }

            $sanitized[] = $attachment_id;
        }

        if ( $field['multiple'] ) 
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
        $value = $value ? (array) $value : array();

        $translated = array();

        foreach ( $value as $attachment_id ) 
        {   
            $translated[] = wp_get_attachment_image( $attachment_id, 'thumbnail' );
        }

        if ( ! $translated ) 
        {
            return '';
        }

        return '<div class="pb-thumbnails"><div class="pb-thumbnail">' . implode( '</div><div class="pb-thumbnail">', $translated ) . '</div>';
    }

    public function enqueue_scripts()
    {
        wp_enqueue_media();
        wp_enqueue_script( 'wp-util' );
    }
}

pb()->field_types->register( 'PB_Image_Field' );

