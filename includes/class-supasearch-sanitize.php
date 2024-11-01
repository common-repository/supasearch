<?php

/**
 * Shared sanitize class.
 *
 * This class defines functions that can sanitize any value.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Sanitize {

    /**
     * Static function which which cleans the passed value based on passed sanitize options.
     *
     * @since    0.1.0
     *
     * @param    string $option_value The value to be clean.
     * @param    array  $sanitizers   The list of sanitize functions to be run on the value
     *
     * @return   string       The convert name.
     */
    public static function clean( $option_value, $sanitizers ) {
        // Loop passed sanitizers
        foreach( $sanitizers as $sanitize ) {
            if( in_array( $sanitize, array( 'text', 'hidden' ) ) ) {
                $option_value = sanitize_text_field( $option_value );
            } elseif( $sanitize === 'textarea' ) {
                // Encodes text for use inside a <textarea> element
                $option_value = esc_textarea( $option_value );
            } elseif( $sanitize === 'comma_separated' ) {
                // Converts string to comma separated list
                $option_value = self::comma_separated( $option_value );
            }
        }

        // Return cleaned value
        return $option_value;
    }

    /**
     * Clean value to be a comma separated list.
     *
     * @since    0.1.0
     *
     * @param    string $option_value The value to be sanitized.
     *
     * @return   array                The sanitized value.
     */
    private function comma_separated( $option_value ) {
        // Converted all non-words to spaces
        $option_value = preg_replace( '/\W/', ' ', $option_value );
        // Converted single / multiple spaces to a comma
        $option_value = preg_replace( '/\s+/', ',', $option_value );
        // Trim value and explode on comma
        $option_value = explode( ',', trim( $option_value ) );
        // Unique array to avoid duplicates and implode back to a comma separated string
        $option_value = implode( ',', array_unique( $option_value ) );

        // Return comma separated string
        return $option_value;
    }
}