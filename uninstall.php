<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://www.supadu.com
 * @since      0.1.0
 * @package    Supasearch
 */

/**
 * If uninstall not called from WordPress, then abort.
 */
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * The class responsible for uninstalling the plugin.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-supasearch-uninstaller.php';

/**
 * Begins execution of code used for deletion of plugin.
 */
Supasearch_Uninstaller::uninstall();