<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueues the admin-specific
 * stylesheet and JavaScript.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Admin {
    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The label of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $plugin_label The label of this plugin.
     */
    private $plugin_label;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $version The current version of this plugin.
     */
    private $version;

    /**
     * The name of the admin options.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $option_name The name of the stored options.
     */
    private $option_name;

    /**
     * The class which controls and creates the different options for the admin of the plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $admin_options The class which controls and creates the different options for the admin of the
     *           plugin.
     */
    private $admin_options;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     *
     * @param    string $plugin_name The name of this plugin.
     * @param    string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->plugin_label = Supasearch_Utilities::get_label_from_name( $this->plugin_name );
        $this->version = $version;
        $this->option_name = Supasearch::get_option_name();

        $this->load_dependencies();
        $this->admin_options = new Supasearch_Admin_Options( $this->plugin_name, $this->version, $this->option_name );
    }

    /**
     * Load the required dependencies for use on the admin facing side of the website.
     *
     * Include the following files that are used for the admin facing side of the website:
     *
     * - Supasearch_Admin_Options    Defines options and default values for the admin area.
     *
     * @since    0.1.0
     * @access   private
     */
    private function load_dependencies() {
        // The class is responsible for defining all options available in the admin area.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-supasearch-admin-options.php';

        // The class is responsible for defining callbacks for all fields in the admin area.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-supasearch-admin-fields.php';
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.1.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/supasearch-admin.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/supasearch-admin.js', array( 'jquery', 'jquery-ui-slider' ), $this->version, true );
    }

    /**
     * Register the admin menu.
     *
     * @since    0.1.0
     */
    public function add_menu() {
        add_options_page(
            esc_html__( $this->plugin_label . ' Settings', $this->plugin_name ),
            $this->plugin_label,
            apply_filters( $this->plugin_name . '-user-privileges', 'manage_options' ),
            $this->plugin_name,
            array( $this, 'options_page' )
        );
    }

    /**
     * Initialise the admin settings.
     *
     * @since    0.1.0
     */
    public function settings_init() {
        $this->register_settings();
        $this->register_sections();
        $this->register_fields();
    }

    /**
     * Display notices
     *
     * @since    0.1.0
     */
    public function notices() {
        if( get_option( Supasearch::get_activation_notice_option_name() ) !== false ) {
            include_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-activation-notice.php';
            delete_option( Supasearch::get_activation_notice_option_name() );
        }
    }

    /**
     * Register the admin settings.
     *
     * @since    0.1.0
     */
    private function register_settings() {
        foreach( $this->admin_options->get_settings_sections() as $section => $config ) {
            register_setting( $this->plugin_name . $section, $this->option_name, array( $this->admin_options, 'validate_options' ) );
        }
    }

    /**
     * Register the admin sections.
     *
     * @since    0.1.0
     */
    private function register_sections() {
        foreach( $this->admin_options->get_settings_sections() as $section => $config ) {
            $this->admin_options->add_settings_section( $this->option_name . $section, $config['settings']['title'], $config['settings']['callback'], $section );
        }
    }

    /**
     * Register the admin section fields.
     *
     * @since    0.1.0
     */
    private function register_fields() {
        foreach( $this->admin_options->get_settings_sections() as $section => $config ) {
            // If the section has no fields then continue
            if( !isset( $config['fields'] ) || empty( $config['fields'] ) ) {
                continue;
            }

            // Loop and add fields
            foreach( $config['fields'] as $key => $value ) {
                $this->admin_options->add_settings_field( $key, $value['title'], $value['callback'], $section, $value['args'] );
            }
        }
    }

    /**
     * Display the settings page view.
     *
     * @since    0.1.0
     */
    public function options_page() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-options.php';
    }
}