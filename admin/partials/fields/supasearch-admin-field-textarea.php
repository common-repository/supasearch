<?php

/**
 * Provides the markup for a textarea field
 *
 * This file is used to markup a textarea field.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin/partials/fields
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */

if( !empty( $atts['label'] ) ) : ?>
    <label for="<?php echo esc_attr( $atts['id'] ); ?>"><?php esc_html_e( $atts['label'], $this->plugin_name ); ?>: </label>
<?php endif; ?>

<textarea
    <?php disabled( $atts['disabled'] ); ?>
    class="<?php echo esc_attr( $atts['class'] ); ?>"
    cols="<?php echo esc_attr( $atts['cols'] ); ?>"
    id="<?php echo esc_attr( $atts['id'] ); ?>"
    name="<?php echo esc_attr( $atts['name'] ); ?>"
    rows="<?php echo esc_attr( $atts['rows'] ); ?>"><?php echo esc_textarea( $atts['value'] ); ?>
</textarea>
<p><span class="description"><?php esc_html_e( $atts['description'], $this->plugin_name ); ?></span></p>