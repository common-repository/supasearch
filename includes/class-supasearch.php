<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch {
    /**
     * The unique identifier of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $plugin_name The string used to uniquely identify this plugin.
     */
    private static $plugin_name = 'supasearch';

    /**
     * The current version of the plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $version The current version of the plugin.
     */
    private static $version = '0.2.0-beta';

    /**
     * The option name for the settings of the plugin.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $version The option name for the settings of the plugin.
     */
    private static $option_name = 'supasearch_options';

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @type     Supasearch_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Supasearch_Utilities        Orchestrates the hooks of the plugin.
     * - Supasearch_Sanitize         Cleans saved data to ensure nothing dangerous is passed to the DB.
     * - Supasearch_Loader           Orchestrates the hooks of the plugin.
     * - Supasearch_i18n             Defines all helper functions.
     * - Supasearch_Base_Model       Defines DB helper functions.
     * - Supasearch_Dictionary       Defines the settings for the dictionary table.
     * - Supasearch_Known            Defines the settings for the known table.
     * - Supasearch_Log              Defines the settings for the log table.
     *
     * Create an instance of the loader which will be used to register the hooks with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function load_dependencies() {
        // The class is responsible for defining helper utility functions used throughout the plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-utilities.php';

        // The class is responsible for santizing values used throughout the plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-sanitize.php';

        // The class is responsible for orchestrating the actions and filters of the core plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-loader.php';

        // The class is responsible for defining internationalization functionality of the plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-i18n.php';

        // The class is responsible for defining base model functionality of the plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-supasearch-base-model.php';

        // The class is responsible for defining the model for the dictionary table.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-supasearch-dictionary.php';

        // The class is responsible for defining the model for the known table.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-supasearch-known.php';

        // The class is responsible for defining the model for the log table.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-supasearch-log.php';

        // Set loader for class to use to register hooks.
        $this->loader = new Supasearch_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Supasearch_i18n class in order to set the domain and to register the hook with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Supasearch_i18n( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Set hooks based on whether you're in the admin area of the plugin or not.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_hooks() {
        if( is_admin() ) {
            $this->define_admin_hooks();
        } else {
            $this->define_public_hooks();
        }

        // Add shared plugin hooks
        $this->loader->add_action( 'plugins_loaded', $this, 'check_plugin_version' );
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_admin_hooks() {
        // Load the class responsible for defining all actions that occur in the admin area.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-supasearch-admin.php';
        // Load the class responsible for defining all ajax .
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-supasearch-admin-ajax.php';

        // Create admin instance
        $plugin_admin = new Supasearch_Admin( $this->get_plugin_name(), $this->get_version() );
        $plugin_admin_ajax = new Supasearch_Admin_Ajax( $this->get_plugin_name(), $this->get_version() );

        // Set admin actions and filters
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'settings_init' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'notices' );
        $this->loader->add_action( 'wp_ajax_supasearch_db_content_sync', $plugin_admin_ajax, 'supasearch_db_content_sync' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_public_hooks() {
        // Load the class responsible for defining all actions that occur in the public-facing side of the site.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-supasearch-public.php';

        // Create public instance
        $plugin_public = new Supasearch_Public( $this->get_plugin_name(), $this->get_version() );

        // Set public actions and filters
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'request', $plugin_public, 'parse_request' );
        $this->loader->add_filter( 'query_vars', $plugin_public, 'add_query_vars' );
        $this->loader->add_filter( 'the_content', $plugin_public, 'hit_counter' );
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.1.0
     *
     * @return    string    The name of the plugin.
     */
    public static function get_plugin_name() {
        return self::$plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.1.0
     *
     * @return    string    The version number of the plugin.
     */
    public static function get_version() {
        return self::$version;
    }

    /**
     * Retrieve the option name for the settings of the plugin.
     *
     * @since     0.1.0
     *
     * @return    string    The option name for the settings of the plugin.
     */
    public static function get_option_name() {
        return self::$option_name;
    }

    /**
     * Retrieve the option name for the activation value of the plugin.
     *
     * @since     0.1.0
     *
     * @return    string    The option name for the activation value of the plugin.
     */
    public static function get_activation_notice_option_name() {
        return self::$option_name . '_activation_notice';
    }

    /**
     * Retrieve the option name for the version value of the plugin.
     *
     * @since     0.2.0
     *
     * @return    string    The option name for the version value of the plugin.
     */
    public static function get_plugin_version_option_name() {
        return self::$option_name . '_plugin_version';
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     0.1.0
     *
     * @return    Supasearch_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.1.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Check current version of the plugin
     *
     * @since    0.2.0
     */
    public function check_plugin_version() {
        if( get_option( Supasearch::get_plugin_version_option_name() ) !== self::$version ) {
            $this->activate_supasearch( true );
        }
    }

    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-supasearch-activator.php
     *
     * @since     0.1.0
     *
     * @param bool $update Flag to state if it's an activation or and update
     */
    public function activate_supasearch( $update = false ) {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-activator.php';
        Supasearch_Activator::activate( $update );
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-supasearch-deactivator.php
     *
     * @since     0.1.0
     */
    public function deactivate_supasearch() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-supasearch-deactivator.php';
        Supasearch_Deactivator::deactivate();
    }

    /**
     * Bootstrap for the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since     0.1.0
     */
    public static function init() {
        // Create new plugin
        $plugin = new self();
        $plugin_file = dirname( plugin_dir_path( __FILE__ ) ) . '/' . $plugin->get_plugin_name() . '.php';

        // Registration activation hooks
        register_activation_hook( $plugin_file, array( $plugin, 'activate_supasearch' ) );
        register_deactivation_hook( $plugin_file, array( $plugin, 'deactivate_supasearch' ) );

        // Run plugin
        $plugin->run();
    }
}