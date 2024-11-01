<?php

/**
 * Fired during plugin deletions.
 *
 * This class defines all code necessary to run during the plugin's deletion.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Uninstaller {
    /**
     * Static function which is run during plugin deletion.
     *
     * @since    0.1.0
     */
    public static function uninstall() {
        // Load dependencies of the uninstaller
        self::load_dependencies();

        // Delete default settings
        delete_option( Supasearch::get_option_name() );

        // Drop a dictionary table so install can re-create the default table data
        Supasearch_Dictionary::drop();
    }

    /**
     * Load the required dependencies for the uninstaller.
     *
     * Include the following files that make up the uninstaller:
     *
     * - Supasearch              Core plugin class.
     * - Supasearch_Base_Model   Defines all functions and properties to be shared between models.
     * - Supasearch_Dictionary   Defines a model for accessing dictionary table.
     *
     * @since    0.1.0
     * @access   private
     */
    private function load_dependencies() {
        // The core plugin class.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch.php';

        // The class responsible for defining base model functionality of the plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-supasearch-base-model.php';

        // The class responsible for defining the model for the dictionary table.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-supasearch-dictionary.php';
    }
}