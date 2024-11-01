<?php

/**
 * Provides a view for the db sync section
 *
 * This file is used to markup the db sync section of the plugin.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin/partials
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */

?>

<p><?php esc_html_e( 'Scrape and add all words from published posts and pages to the spell checker dictionary', $this->plugin_name ); ?></p>

<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><?php esc_html_e( $label, $this->plugin_name ); ?></th>
            <td>
                <input type="button" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="button button-primary" value="<?php esc_html_e( $label, $this->plugin_name ); ?>">
                <p id="<?php echo $id; ?>_progress"></p>
                <div id="<?php echo $id; ?>_progress_bar">
                    <div id="<?php echo $id; ?>_progress_bar_fill"></div>
                </div>
            </td>
        </tr>
    </tbody>
</table>