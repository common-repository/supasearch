<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.supadu.com/supasearch
 * @since             0.1.0
 * @package           Supasearch
 *
 * @wordpress-plugin
 * Plugin Name:       Supasearch
 * Plugin URI:        http://www.supadu.com/supasearch
 * Description:       Supasearch enhances the default WordPress search.
 * Version:           0.2.0-beta
 * Author:            David Kane (Supadu)
 * Author URI:        http://www.supadu.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       supasearch
 * Domain Path:       /languages
 */

/**
 * If this file is called directly, abort.
 */
if( !defined( 'WPINC' ) ) {
    die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-supasearch.php';

/**
 * Begins execution of the plugin.
 */
Supasearch::init();