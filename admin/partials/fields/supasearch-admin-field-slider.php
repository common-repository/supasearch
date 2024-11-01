<?php

/**
 * Provides the markup for a slider field
 *
 * This file is used to markup a slider field.
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
    type="hidden"
    id="<?php echo esc_attr( $atts['id'] ); ?>"
    name="<?php echo esc_attr( $atts['name'] ); ?>"
    value="<?php echo esc_attr( $atts['value'] ); ?>"
/>
<div id="<?php echo esc_attr( $atts['id'] ); ?>-slider" class="<?php echo $this->plugin_name; ?>-slider" data-name="<?php echo esc_attr( $atts['id'] ); ?>">
    <div id="<?php echo esc_attr( $atts['id'] ); ?>-slider__handle" class="<?php echo $this->plugin_name; ?>-slider__handle ui-slider-handle" data-type="<?php echo esc_attr( $atts['data-type'] ); ?>"></div>
</div>
<p><span class="description"><?php esc_html_e( $atts['description'], $this->plugin_name ); ?></span></p>