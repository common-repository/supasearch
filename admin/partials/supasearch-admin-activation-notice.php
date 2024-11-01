<?php

/**
 * Provides the display for the activation notice
 *
 * This file is used to markup the activation notice of the plugin.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin/partials
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */

// Get section and active tab
$sections = $this->admin_options->get_settings_sections();
$options_page_url =  menu_page_url( $this->plugin_name, false );
?>

<?php if(isset($sections['_db_sync'])) : ?>
    <div class="updated notice is-dismissible">
        <?php; ?>
        <p><?php _e( "You've activated {$this->plugin_label}! For improved results try syncing your content <a href=\"{$options_page_url}&tab=_db_sync\">here</a>.", $this->plugin_name ); ?></p>
    </div>
<?php endif; ?>