<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_i18n {
    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $version The current version of this plugin.
     */
    private $version;


    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     *
     * @param    string $plugin_name The name of the plugin.
     * @param    string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.1.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( $this->plugin_name, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
    }
}