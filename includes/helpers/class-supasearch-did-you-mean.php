<?php

/**
 * The did you mean?
 *
 * Searches the database for the nearest matching post title.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes/helpers
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Did_You_Mean {
    /**
     * Queries the DB for any posts with some of the words in the title
     *
     * @since  0.1.0
     *
     * @param  string $query The query term run for the search.
     *
     * @return array
     */
    public static function calculate( $query ) {
        global $wpdb;

        // Correct query
        $correct_query = Supasearch_Spell_Checker::check( $query );

        // Get closest matching post title sorted by most viewed post
        $did_you_mean = self::check_previous_searches( $query, $correct_query );

        // If suggestion is not the same as the query return
        if( $did_you_mean !== $correct_query ) {
            return $did_you_mean;
        }

        // Set up variables and fetch stop words
        $where_terms = array();
        $did_you_mean = null;
        $stop_words = Supasearch_Utilities::get_stop_words();

        // Build sql query condition ignore stop words
        foreach( explode( ' ', $correct_query ) as $word ) {
            if( !preg_grep( "/{$word}/i", $stop_words ) ) {
                $where_terms["{$word} %"] = "post_title LIKE %s";
                $where_terms["% {$word} %"] = "post_title LIKE %s";
                $where_terms["% {$word}"] = "post_title LIKE %s";
            }
        }

        // If a condition was created calculate closest post
        if( !empty( $where_terms ) ) {
            // Build sql state
            $sql = "SELECT post_title, IFNULL(meta_value, 0) AS hit_counter 
                    FROM {$wpdb->prefix}posts 
                    LEFT JOIN {$wpdb->prefix}postmeta ON ID = post_id AND meta_key = '" . Supasearch_Public::HIT_COUNTER . "' 
                    WHERE post_status = 'publish' AND post_type IN ('page', 'post') AND (" . implode( ' OR ', $where_terms ) . ")";

            // Get results from query and set up array for results
            $posts = $wpdb->get_results( $wpdb->prepare( $sql, array_keys( $where_terms ) ) );
            $did_you_means = array();

            // Loop posts and build array for get_closest_phonetically function to process
            foreach( $posts as $post ) {
                // Check post title is not empty
                if( trim( $post->post_title ) === '' ) {
                    continue;
                }

                // Add to array to be processed
                $did_you_means[] = (object) array( 'word' => $post->post_title, 'count' => $post->hit_counter );
            }

            // Get min match percentage from the settings
            $min_match_percentage = Supasearch_Utilities::get_min_match_percentage();

            // Get closest matching post title sorted by most viewed post
            $did_you_mean = Supasearch_Spell_Checker::get_closest_phonetically( $did_you_means, $correct_query, (float) $min_match_percentage );

            // If the suggestion is the same as the original query then return null
            if( $did_you_mean === $query ) {
                $did_you_mean = null;
            }
        }

        // Check if term has results before suggesting
        $result = Supasearch_Log::get( md5( $did_you_mean ) );

        // Set to null if no results found
        if( $result !== null && (int) $result->has_results === 0 ) {
            $did_you_mean = null;
        }

        // Return suggestion
        return $did_you_mean;
    }

    /**
     * Queries the DB for any posts with some of the words in the title
     *
     * @since  0.2.0
     *
     * @param  string $query         The query term run for the search.
     * @param  string $correct_query The corrected query term run for the search.
     *
     * @return string
     */
    public static function check_previous_searches( $query, $correct_query = null ) {
        // If corrected_query is not past then use the original
        $correct_query = $correct_query === null ? $query : $correct_query;

        // Get previous searches which have results
        $previous_searches = Supasearch_Log::get( array(), 'has_results = 1' );

        // Make sure previouse searches is an array to be looped so you do not end up looping the columns of and object
        if( !is_array( $previous_searches ) ) {
            $previous_searches = array( $previous_searches );
        }

        // Set up potentional suggestions
        $did_you_means = array();

        // Loop through previous searches
        foreach( $previous_searches as $search ) {
            // Add to array to be processed
            if( $search->search === $query || $search->search === $correct_query ) {
                continue;
            }

            // Add to potentional did you mean suggestions
            $did_you_means[] = (object) array( 'word' => $search->search, 'count' => $search->count );
        }

        // Get closest matching post title sorted by most viewed post
        return Supasearch_Spell_Checker::get_closest_phonetically( $did_you_means, $correct_query, (float) Supasearch_Utilities::get_previous_query_closeness(), (int) Supasearch_Utilities::get_min_number_hits() );
    }
}