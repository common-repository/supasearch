<?php

/**
 * The options and default values of the plugin.
 *
 * Defines options of the plugin and their default values.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Admin_Options {
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
     * The group name of the admin options.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $option_name The current version of this plugin.
     */
    private $option_name;

    /**
     * The admin settings values.
     *
     * @since    0.1.0
     * @access   private
     * @type     array $options The settings for the plugin.
     */
    private $options;

    /**
     * The fields and their defaults.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $options The current version of this plugin.
     */
    private $settings_sections;

    /**
     * The class which controls and creates the callback functions for fields.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $admin_options The current version of this plugin.
     */
    private $admin_fields;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     *
     * @param    string $plugin_name The name of the plugin.
     * @param    string $version     The version of this plugin.
     * @param    string $option_name The version of this plugin.
     */
    public function __construct($plugin_name, $version, $option_name) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->option_name = $option_name;
        $this->options = get_option( $this->option_name );
        $this->admin_fields = new Supasearch_Admin_Fields( $this->plugin_name, $this->version, $this->option_name );
        $this->settings_sections = $this->set_settings_sections();
    }

    /**
     * Sets all the fields that will be required from the options page of the plugin.
     *
     * @return    array The full list of options
     */
    private function set_settings_sections() {
        return array(
            '_db_sync'       => array(
                'settings' => array(
                    'title'    => esc_html__( 'Database Content Sync', $this->plugin_name ),
                    'callback' => array( $this, 'section_db_sync' )
                )
            ),
            '_spell_checker' => array(
                'settings' => array(
                    'title'    => esc_html__( 'Spell Checker', $this->plugin_name ),
                    'callback' => array( $this, 'section_spell_checker' )
                ),
                'fields'   => array(
                    'expletive_words' => array(
                        'title'    => esc_html__( 'Expletive Words', $this->plugin_name ),
                        'callback' => array( $this->admin_fields, 'field_textarea' ),
                        'sanitize' => array( 'textarea', 'comma_separated' ),
                        'args'     => array(
                            'description' => esc_html__( 'This is a comma separated list of expletive words for the spell corrector to ignore. If you want to modify these why not try the PRO version?', $this->plugin_name ),
                            'value'       => implode( ',', Supasearch_Utilities::get_expletive_words() ),
                            'disabled'    => true
                        )
                    ),
                    'stop_words'      => array(
                        'title'    => esc_html__( 'Stop Words', $this->plugin_name ),
                        'callback' => array( $this->admin_fields, 'field_textarea' ),
                        'sanitize' => array( 'textarea', 'comma_separated' ),
                        'args'     => array(
                            'description' => esc_html__( 'This is a comma separated list of stop words for the spell corrector to ignore. If you want to modify these why not try the PRO version?', $this->plugin_name ),
                            'value'       => implode( ',', Supasearch_Utilities::get_stop_words() ),
                            'disabled'    => true
                        )
                    )
                )
            ),

            '_did_you_mean' => array(
                'settings' => array(
                    'title'    => esc_html__( 'Did You Mean', $this->plugin_name ),
                    'callback' => array( $this, 'section_did_you_mean' )
                ),
                'fields'   => array(
                    'min_match_percentage' => array(
                        'title'    => esc_html__( 'Minimum Match Percentage', $this->plugin_name ),
                        'callback' => array( $this->admin_fields, 'field_slider' ),
                        'sanitize' => array( 'hidden' ),
                        'args'     => array(
                            'description' => esc_html__( 'From 1% to 99% set how close a suggestion must be to the original query.', $this->plugin_name ),
                            'value'       => Supasearch_Utilities::get_min_match_percentage(),
                            'data-type'   => 'percentage'
                        )
                    ),
                    'nothing_found'        => array(
                        'title'    => esc_html__( 'Nothing Found Text', $this->plugin_name ),
                        'callback' => array( $this->admin_fields, 'field_text' ),
                        'sanitize' => array( 'text' ),
                        'args'     => array(
                            'description' => esc_html__( 'This is the text that will display when no did you mean suggestions are found.', $this->plugin_name ),
                            'value'       => 'Nothing Found'
                        )
                    ),
                    'previous_query_closeness' => array(
                        'title'    => esc_html__( 'Previous query match percentage', $this->plugin_name ),
                        'callback' => array( $this->admin_fields, 'field_slider' ),
                        'sanitize' => array( 'hidden' ),
                        'args'     => array(
                            'description' => esc_html__( 'From 1% to 99% set how close a previously searched term must be to the original query.', $this->plugin_name ),
                            'value'       => Supasearch_Utilities::get_previous_query_closeness(),
                            'data-type'   => 'percentage'
                        )
                    ),
                    'min_number_hits' => array(
                        'title'    => esc_html__( 'Minimum number of previous searches', $this->plugin_name ),
                        'callback' => array( $this->admin_fields, 'field_slider' ),
                        'sanitize' => array( 'hidden' ),
                        'args'     => array(
                            'description' => esc_html__( 'From 1 to 100 how many times must a term have been search by users before using it as a popular suggestion.', $this->plugin_name ),
                            'value'       => Supasearch_Utilities::get_min_number_hits()
                        )
                    )
                )
            )
        );
    }

    /**
     * Returns fields to be used for settings section
     *
     * @return array
     */
    public function get_settings_sections() {
        return $this->settings_sections;
    }

    /**
     * Adds settings section to the admin
     *
     * @param     string $id       ID of the section
     * @param     string $title    Title of the section
     * @param     array  $callback Function used to render the section
     * @param     string $section  Section name for the settings to appear on
     */
    public function add_settings_section($id, $title, $callback, $section) {
        add_settings_section( $id, $title, $callback, $this->plugin_name . $section );
    }

    /**
     * Adds settings fields to the admin
     *
     * @param     array $id       ID of the field
     * @param     array $title    Title of the field
     * @param     array $callback Function used to render the field
     * @param     array $section  Section the field belongs to
     * @param     array $args     Arguments for the field settings
     */
    public function add_settings_field($id, $title, $callback, $section, $args) {
        add_settings_field( $id, $title, $callback, $this->plugin_name . $section, $this->option_name . $section, array( 'id' => "{$this->plugin_name}_{$id}" ) + $args );
    }

    /**
     * Display the spell checker section view.
     *
     * @since    0.1.0
     */
    public function section_spell_checker() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-options-section-spell-checker.php';
    }

    /**
     * Display the did you mean section view.
     *
     * @since    0.1.0
     */
    public function section_did_you_mean() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-options-section-did-you-mean.php';
    }

    /**
     * Display the db sync section view.
     *
     * @param    array $args Arrray of section settings
     *
     * @since    0.1.0
     */
    public function section_db_sync($args) {
        /** @noinspection variable used inside included partial PhpUnusedLocalVariableInspection */
        $id = str_replace( $this->option_name, $this->plugin_name, $args['id'] );
        /** @noinspection variable used inside included partial PhpUnusedLocalVariableInspection */
        $label = 'Sync DB Content';
        include_once plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-options-section-db-sync.php';
    }

    /**
     * Validate saved plugin options
     *
     * @param     array $options Arrray of submitted options
     *
     * @return    array Array of validated options
     */
    public function validate_options($options) {
        // Create empty array to store validated options
        $validated_options = array();

        // Loop fields and validate based on their sanitize options
        foreach( $this->settings_sections as $section => $config ) {
            // If the section has no fields then continue
            if( !isset( $config['fields'] ) || empty( $config['fields'] ) ) {
                continue;
            }

            // Loop fields to sanitize
            foreach( $config['fields'] as $key => $value ) {
                if( isset( $options[$key] ) && !empty( $value['sanitize'] ) ) {
                    // Sanitize option value
                    $validated_options[$key] = Supasearch_Sanitize::clean( $options[$key], $value['sanitize'] );
                }
            }
        }

        // Return validates options
        return $this->merge_options( $this->options, $validated_options );
    }

    /**
     * Merge saved plugin options with existing plugin options
     *
     * @param     array $existing Arrray of existing options
     * @param     array $input    Arrray of submitted and validated options
     *
     * @return    array Array of merged options
     */
    function merge_options($existing, $input) {
        // If they're not both arrays then something is wrong and the settings will be reset
        if( !is_array( $existing ) && !is_array( $input ) ) {
            return array();
        }

        // If there is no existing array just return the new array
        if( !is_array( $existing ) ) {
            return $input;
        }

        // Merge existing and new if both exist
        return array_merge( $existing, $input );
    }
}