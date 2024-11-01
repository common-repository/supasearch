<?php

/**
 * Provides a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin/partials
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */

// Get section and active tab
$sections = $this->admin_options->get_settings_sections();
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : array_keys( $sections )[0];

?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <h2 class="nav-tab-wrapper">
        <?php foreach( $sections as $section => $config ) : ?>
            <?php
            // Build link for tab
            $link = "?page={$this->plugin_name}&tab={$section}";
            $class = $active_tab !== $section ? 'nav-tab' : 'nav-tab nav-tab-active';
            $title = isset( $config['settings'] ) && isset( $config['settings']['title'] ) ? $config['settings']['title'] : $section;
            ?>
            <a href="<?php echo $link; ?>" class="<?php echo $class; ?>"><?php echo $title; ?></a>
        <?php endforeach; ?>
    </h2>
    <!--suppress HtmlUnknownTarget -->
    <form method="post" action="options.php">
        <?php
        // Print settings
        settings_fields( $this->plugin_name . $active_tab );
        do_settings_sections( $this->plugin_name . $active_tab );

        // If the active section has fields then show the save button
        if( isset( $sections[$active_tab]['fields'] ) ) {
            submit_button( 'Save Settings' );
        }
        ?>
    </form>
</div>