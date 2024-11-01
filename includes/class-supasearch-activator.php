<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Activator {
    /**
     * Static function which is run during plugin activation.
     *
     * @since    0.1.0
     *
     * @param bool $update Flag to state if it's an activation or and update
     */
    public static function activate( $update ) {
        // The class responsible for defining helper utility functions used throughout the plugin.
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // The class responsible for defining the plugin name, version and hooks.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-utilities.php';

        // The class responsible for defining helper utility functions used throughout the plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-utilities.php';

        // Create dictionary table
        self::create_dictionary_table();

        // Create known table
        self::create_known_table();

        // Create log table
        self::create_log_table();

        // Update current version
        update_option( Supasearch::get_plugin_version_option_name(), Supasearch::get_version() );

        // Add activation notice settings if not and update
        if( $update !== true ) {
            add_option( Supasearch::get_activation_notice_option_name(), true );
        }
    }

    /**
     * Create table to store dictionary words.
     *
     * @since    0.1.0
     */
    private function create_dictionary_table() {
        global $wpdb;

        // Set environment parameters
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = Supasearch_Dictionary::get_table_name();
        $importData = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ? true : false;

        // Build create table query
        $sql = "CREATE TABLE {$table_name} (
            `word` VARCHAR(150) NOT NULL,
            `original_count` BIGINT(20) NOT NULL,
            `temp_count` BIGINT(20) NOT NULL,
            `count` BIGINT(20) NOT NULL,
            UNIQUE KEY word (word),
            KEY count (count)
        ) {$charset_collate};";

        // Run query
        $result = dbDelta( $sql );

        // Run query to create table
        if( !empty( $result ) && $importData === true ) {
            // Set directory and file paths to data
            $directory = plugin_dir_path( dirname( __FILE__ ) ) . 'data';
            $file = $directory . '/supasearch_dictionary_default_data.txt';

            // Check if file exists
            if( file_exists( $file ) ) {
                // Build insert data query
                $data = file_get_contents( $file );
                $sql = "INSERT INTO {$table_name} (`word`, `original_count`, `temp_count`, `count`) VALUES {$data};";

                // Run query to insert default data
                if( $wpdb->query( $sql ) !== false ) {
                    // Remove file when complete
                    if( is_writable( $file ) && is_writable( $directory ) && is_writable( dirname( $directory ) ) ) {
                        unlink( $file );
                        unlink( $directory . '/index.php' );
                        rmdir( $directory );
                    } else {
                        self::error_handler( 'Error: Incorrect file permissions - plugin directory must be writable' );
                    }
                } else {
                    self::error_handler( 'Error: Inserting dictionary data failed - ' . $wpdb->last_error );
                }
            } else {
                self::error_handler( 'Error: Dictionary data file not found - please delete and re-install the plugin' );
            }
        } else {
            if( empty( $result ) ) {
                self::error_handler( 'Error: Creating dictionary table failed' );
            }
        }
    }

    /**
     * Create table to store corrected words.
     *
     * @since    0.1.0
     */
    private function create_known_table() {
        global $wpdb;

        // Set environment parameters
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = Supasearch_Known::get_table_name();

        // Build create table query
        $sql = "CREATE TABLE {$table_name} (
            `word` VARCHAR(150) NOT NULL,
            `correction` VARCHAR(150) NOT NULL,
            UNIQUE KEY word (word)
        ) {$charset_collate};";

        // Run query
        $result = dbDelta( $sql );

        // Check for errors
        if( empty( $result ) ) {
            self::error_handler( 'Error: Creating known table failed' );
        }
    }

    /**
     * Create table to log searches on the site.
     *
     * @since    0.1.0
     */
    private function create_log_table() {
        global $wpdb;

        // Set environment parameters
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = Supasearch_Log::get_table_name();

        // Build create table query
        $sql = "CREATE TABLE {$table_name} (
                `identifier` VARCHAR(50) NOT NULL,
                `search` LONGTEXT NOT NULL,
                `count` BIGINT(20) NOT NULL DEFAULT 1,
                `has_results` TINYINT(1) NOT NULL DEFAULT 0,
                UNIQUE KEY identifier (identifier)
            ) {$charset_collate};";

        // Run query
        $result = dbDelta( $sql );

        // Check for errors
        if( empty( $result ) ) {
            self::error_handler( 'Error: Creating log table failed' );
        }
    }

    /**
     * Resets activation process and deactivates the plugin
     *
     * @since    0.1.0
     *
     * @param    string $message The message to be display for the error.
     */
    private function error_handler( $message ) {
        // The class responsible for uninstalling the plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-uninstaller.php';

        // Resets activation code.
        Supasearch_Uninstaller::uninstall();

        // Deactivate plugin
        deactivate_plugins( dirname( plugin_dir_path( __FILE__ ) ) . '/' . Supasearch::get_plugin_name() . '.php' );

        // Display error message
        wp_die( __( $message, Supasearch::get_plugin_name() ) );
    }
}