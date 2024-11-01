<?php

/**
 * Provides the markup for a text field
 *
 * This file is used to markup a text field.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin/partials/fields
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */

if( !empty( $atts['label'] ) ) : ?>
    <label for="<?php echo esc_attr( $atts['id'] ); ?>"><?php esc_html_e( $atts['label'], $this->plugin_name ); ?>: </label>
<?php endif; ?>

<input
    <?php disabled( $atts['disabled'] ); ?>
    type="text"
    class="<?php echo esc_attr( $atts['class'] ); ?>"
    id="<?php echo esc_attr( $atts['id'] ); ?>"
    name="<?php echo esc_attr( $atts['name'] ); ?>"
    value="<?php echo esc_attr( $atts['value'] ); ?>"
/>
<p><span class="description"><?php esc_html_e( $atts['description'], $this->plugin_name ); ?></span></p>