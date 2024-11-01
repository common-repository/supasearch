<?php

/**
 * The admin-specific ajax functionality of the plugin.
 *
 * Defines all the AJAX call backs used for the admin
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/admin
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Admin_Ajax {
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
     * @param    string $plugin_name The name of this plugin.
     * @param    string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Scrape the database for new words to add to the dictionary via an AJAX call.
     *
     * @since    0.1.0
     */
    public function supasearch_db_content_sync() {
        global $wpdb;

        // Set limit and offset
        $limit = 500;
        $offset = isset( $_GET['supasearch_sync_offset'] ) ? (int) $_GET['supasearch_sync_offset'] : 0;

        // If offset is 0 it's the start of the sync so reset temp count to the original
        if( $offset === 0 ) {
            Supasearch_Dictionary::copy_column( 'temp_count', 'original_count' );
        }

        // Fetch chunk of posts and get total
        $sql = "FROM {$wpdb->prefix}posts WHERE post_status = 'publish' AND post_type IN ('page', 'post') ORDER BY ID";
        $total = (int) $wpdb->get_var( "SELECT COUNT(*) {$sql}" );
        $posts = $wpdb->get_results( $wpdb->prepare( "SELECT post_title, post_content {$sql} LIMIT {$limit} OFFSET %d", $offset ) );
        $words = array();

        // Loop through posts to get words from titles and content
        foreach( $posts as $post ) {
            // Get count of words from text
            $word_count = array_count_values( str_word_count( strtolower( strip_tags( $post->post_title . ' ' . $post->post_content ) ), 1 ) );

            // Increment word counters
            foreach( $word_count as $key => $value ) {
                // Strip of any non-alpha characters from the beginning and end
                $key = preg_replace( "/^[^a-z]+|[^a-z]+$/", '', $key );

                // Only continue if the word is a word
                if( trim( $key ) === '' ) {
                    continue;
                }

                // Add word count to array or increment if it already exists
                $words[$key] = isset( $words[$key] ) ? $words[$key] + $value : $value;
            }
        }

        // Loop all words and update DB with new counts
        foreach( $words as $key => $value ) {
            Supasearch_Dictionary::insert_on_duplicate_key( array( $key, 0, $value, 0 ), 'temp_count = temp_count + ' . $value );
        }

        // Calculate progress and new offsets
        $offset = $offset + $limit;
        $completion = floor( ( $offset / $total ) * 100 );
        $completion = $completion > 100 ? 100 : $completion;

        // Complete then truncate the known table to clear previous corrects and copy the temp count column to the live count column
        if( $completion === 100 ) {
            Supasearch_Known::truncate();
            Supasearch_Dictionary::copy_column( 'count', 'temp_count' );
        }

        // Return JSON for response
        wp_send_json( array(
            'status'   => 'success',
            'progress' => array(
                'offset'     => $offset,
                'percentage' => $completion
            )
        ) );
    }
}