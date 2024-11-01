<?php

/**
 * The callback functions for field types
 *
 * Defines callback functions for all fields.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Admin_Fields {
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
     * The saved values of the admin options.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $options The current version of this plugin.
     */
    private $options;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     *
     * @param    string $plugin_name The name of the plugin.
     * @param    string $version     The version of this plugin.
     * @param    string $option_name The version of this plugin.
     */
    public function __construct( $plugin_name, $version, $option_name ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->option_name = $option_name;
        $this->options = get_option( $this->option_name );
    }

    /**
     * Creates a text field
     *
     * @param     array $args The arguments for the field
     */
    public function field_text( $args ) {
        // Get field name
        $field_name = $this->get_field_name( $args['id'] );

        // Set defaults
        $defaults = array(
            'class'       => 'regular-text',
            'description' => '',
            'disabled'    => false,
            'label'       => '',
            'name'        => $this->option_name . '[' . $field_name . ']',
            'value'       => ''
        );

        // Add filter to allow defaults to be overridden
        apply_filters( $this->plugin_name . '-field-text-options-defaults', $defaults );

        // Merge passed arguments with defaults
        $atts = wp_parse_args( $args, $defaults );

        // If value has been previously save then set value
        if( !empty( $this->options[$field_name] ) ) {
            $atts['value'] = $this->options[$field_name];
        }

        // Include view for textarea field
        include plugin_dir_path( __FILE__ ) . 'partials/fields/' . $this->plugin_name . '-admin-field-text.php';
    }

    /**
     * Creates a textarea field
     *
     * @param     array $args The arguments for the field
     */
    public function field_textarea( $args ) {
        // Get field name
        $field_name = $this->get_field_name( $args['id'] );

        // Set defaults
        $defaults = array(
            'class'       => 'large-text',
            'cols'        => 50,
            'description' => '',
            'disabled'    => false,
            'label'       => '',
            'name'        => $this->option_name . '[' . $field_name . ']',
            'rows'        => 10,
            'value'       => ''
        );

        // Add filter to allow defaults to be overridden
        apply_filters( $this->plugin_name . '-field-textarea-options-defaults', $defaults );

        // Merge passed arguments with defaults
        $atts = wp_parse_args( $args, $defaults );

        // If value has been previously save then set value
        if( !empty( $this->options[$field_name] ) ) {
            $atts['value'] = $this->options[$field_name];
        }

        // Include view for textarea field
        include plugin_dir_path( __FILE__ ) . 'partials/fields/' . $this->plugin_name . '-admin-field-textarea.php';
    }

    /**
     * Creates a slider based field
     *
     * @param     array $args The arguments for the field
     */
    public function field_slider( $args ) {
        // Get field name
        $field_name = $this->get_field_name( $args['id'] );

        // Set defaults
        $defaults = array(
            'description' => '',
            'disabled'    => false,
            'label'       => '',
            'name'        => $this->option_name . '[' . $field_name . ']',
            'value'       => '',
            'data-type'   => 'plain'
        );

        // Add filter to allow defaults to be overridden
        apply_filters( $this->plugin_name . '-field-slider-options-defaults', $defaults );

        // Merge passed arguments with defaults
        $atts = wp_parse_args( $args, $defaults );

        // If value has been previously save then set value
        if( !empty( $this->options[$field_name] ) ) {
            $atts['value'] = $this->options[$field_name];
        }

        // Include view for textarea field
        include plugin_dir_path( __FILE__ ) . 'partials/fields/' . $this->plugin_name . '-admin-field-slider.php';
    }

    /**
     * Strips plugin name from ID for the field name
     *
     * @param     string $id The current name for the field
     *
     * @return    string The new name for the field
     */
    private function get_field_name( $id ) {
        return str_replace( $this->plugin_name . '_', '', $id );
    }
}