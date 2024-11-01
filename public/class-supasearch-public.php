<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-specific stylesheet and JavaScript.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/public
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Public {
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
     * The loader that's responsible for maintaining and registering hooks that power the public side of the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @type     Supasearch_Loader $loader Maintains and registers hooks for the public side of the plugin.
     */
    protected $loader;

    /**
     * The query object of a search.
     *
     * @since    0.1.0
     * @access   private
     * @type     WP_Query $search_query The current search query object.
     */
    private $search_query;

    /**
     * The string of the correct misspelling.
     *
     * @since    0.1.0
     * @access   private
     * @type     string $corrected_term The fixed search term.
     */
    private static $corrected_term;

    /**
     * The flag to decide whether or not to suggest a did you mean.
     *
     * @since    0.2.0
     * @access   private
     * @type     boolean $ignore_did_you_mean Flag to ignore did you mean or not.
     */
    private static $ignore_did_you_mean;

    /**
     * The string the postmeta key for tracking post views.
     *
     * @since    0.1.0
     * @type     string HIT_COUNTER Name of the postmeta key for tracking post views.
     */
    const HIT_COUNTER = '_supasearch-hit-counter';

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

        $this->load_dependencies();
    }

    /**
     * Load the required dependencies for use on the public facing side of the website.
     *
     * Include the following files that are used for the public facing side of the website:
     *
     * - Supasearch_Spell_Checker   Compares common words to determine a misspelling
     * - Supasearch_Did_You_Mean    Calculates closest post and suggests it as an alternative
     *
     * @since    0.1.0
     * @access   private
     */
    private function load_dependencies() {
        // The class is responsible for the logic of the spell checker.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/helpers/class-supasearch-spell-checker.php';

        // The class is responsible for the logic of the did you mean suggestions.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/helpers/class-supasearch-did-you-mean.php';
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.1.0
     * @access   public
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/supasearch-public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.1.0
     * @access   public
     */
    public function enqueue_scripts() {
    }

    /**
     * Add additional query parameters to be registed by the plugin.
     *
     * @since    0.1.0
     *
     * @param    array $query_vars The current query vars.
     *
     * @return   array
     */
    public function add_query_vars( $query_vars ) {
        return array_merge( $query_vars, array( 'ims', 'idmy' ) );
    }

    /**
     * Add hooks to be processed only when ignore parameter has not been set.
     *
     * @since    0.1.0
     *
     * @param    array $query_vars The query parameters of the request.
     *
     * @return   array
     */
    public function parse_request( $query_vars ) {
        if( !isset( $query_vars['ims'] ) ) {
            // Set loader for class to use to register hooks
            $this->loader = new Supasearch_Loader();

            // Add hooks as ignore has not be found
            $this->loader->add_action( 'pre_get_posts', $this, 'pre_get_posts' );
            $this->loader->add_filter( 'posts_results', $this, 'posts_results' );

            // Run loader
            $this->loader->run();
        }

        // Ignore did you mean if param is set
        self::$ignore_did_you_mean = isset( $query_vars['idmy'] ) ? true : false;

        // Return query vars
        return $query_vars;
    }

    /**
     * Is view a post or a page get post ID and increment hit counter
     *
     * @since    0.1.0
     */
    public function hit_counter() {
        if( is_single() || is_page() ) {
            global $post;

            if( $post && isset( $post->ID ) ) {
                $hits = get_post_meta( $post->ID, self::HIT_COUNTER, true );
                $hits = $hits ? $hits + 1 : 1;
                update_post_meta( $post->ID, self::HIT_COUNTER, $hits );
            }
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.1.0
     *
     * @param    WP_Query $query The current search query object.
     */
    public function pre_get_posts( $query ) {
        if( $query->is_search() ) {
            // Store search query
            $this->search_query = $query->query_vars;

            // Track search query
            if( isset( $this->search_query['s'] ) && self::$corrected_term === null ) {
                Supasearch_Log::insert_on_duplicate_key( $this->search_query['s'], 'count = count + 1' );
            }
        }
    }

    /**
     * Callback function for the posts_results hook to process term correction
     *
     * @since    0.1.0
     *
     * @param    array $posts The array of posts returned from original search query.
     *
     * @return   array
     */
    public function posts_results( $posts ) {
        // Check current query
        $current_query = $this->search_query !== null && isset( $this->search_query['s'] ) ? trim( $this->search_query['s'] ) : null;

        // Only proceed if no results found and in a search query
        if( count( $posts ) > 0 || $current_query === null ) {

            // Track with search query has results
            if( $current_query !== null && count( $posts ) > 0 ) {
                Supasearch_Log::insert_on_duplicate_key( $this->search_query['s'], 'has_results = 1' );
            }

            // Results found so return posts
            return $posts;
        }

        // Check if there is a match on a past search
        self::$corrected_term = Supasearch_Did_You_Mean::check_previous_searches( $current_query );

        // If correction still matches the current query pass through the spell checker
        if( self::$corrected_term === $current_query ) {
            self::$corrected_term = Supasearch_Spell_Checker::check( $current_query );
        }

        // If correction still matches the current query then return the current results
        if( self::$corrected_term === $current_query ) {
            return $posts;
        }

        // Remove filter to stop duplicate call on new query
        $this->loader->remove_filter( 'posts_results' );

        // Update search query with new term
        $this->search_query['s'] = self::$corrected_term;

        // Run new query
        $query = new WP_Query( $this->search_query );

        // Return posts
        return $query->posts;
    }

    /**
     * Display 'searched for' text with spelling correction included
     *
     * @since    0.1.0
     *
     * @param    string  $query  The query term run for the search.
     * @param    string  $before The HTML prefix for the term for display.
     * @param    string  $after  The HTML suffix for the term for display.
     * @param    boolean $echo   Determines if the HTML should be printed of returned.
     *
     * @return   string
     */
    public static function misspelling( $query, $before = 'Showing results for <span>', $after = '</span>', $echo = true ) {
        // If the query has been correct then prepare display text
        if( self::$corrected_term !== null && self::$corrected_term !== $query ) {
            // Get site url for links
            $siteUrl = get_bloginfo( 'url' );

            // Split corrected terms into words
            $corrected_words = explode( ' ', self::$corrected_term );

            // Get the words which have been changed
            $corrected_words_diff = array_diff( $corrected_words, explode( ' ', $query ) );

            // Loop though corrected words and load any changed words into the partial
            foreach( $corrected_words as $key => $value ) {
                // Only process changed words
                if( in_array( $value, $corrected_words_diff ) ) {
                    // Overwrite word with partial HTML output
                    $corrected_words[$key] = Supasearch_Utilities::get_partial( 'corrected-word', array( 'word' => $value ) );
                }
            }

            // Prepare link for corrected term and apply filter
            $url = apply_filters( 'supasearch_misspelling_url', esc_attr( add_query_arg( array( 's' => urlencode( self::$corrected_term ) ), $siteUrl ) ) );

            // Get partial HTML output for corrected query
            $result = Supasearch_Utilities::get_partial( 'showing-results-for', array(
                'prefix' => $before,
                'link'   => $url,
                'query'  => implode( ' ', $corrected_words ),
                'suffix' => $after
            ) );

            // Prepare link for ignore corrected term and apply filter
            $url = apply_filters( 'supasearch_ignore_misspelling_url', esc_attr( add_query_arg( array( 's' => urlencode( $query ), 'ims' => 1 ), $siteUrl ) ) );

            // Get partial HTML output for ignore corrected query
            $result .= Supasearch_Utilities::get_partial( 'search-instead-for', array(
                'link'  => $url,
                'query' => $query
            ) );
        } else {
            // Set result to original query
            $result = "{$before}{$query}{$after}";
        }

        // Apply filter to result
        $result = apply_filters( 'supasearch_misspelling_suggestion', $result );

        // Print result if echo is set to true
        if( $echo ) {
            echo $result;
        }

        // Return result
        return $result;
    }

    /**
     * Display 'did you mean' text when no results found
     *
     * @since    0.1.0
     *
     * @param    string  $query  The query term run for the search.
     * @param    string  $before The HTML prefix for the did you mean for display.
     * @param    string  $after  The HTML suffix for the did you mean for display.
     * @param    boolean $echo   Determines if the HTML should be printed of returned.
     *
     * @return   string
     */
    public static function did_you_mean( $query, $before = 'Did you mean: <span>', $after = '</span>', $echo = true ) {
        // Calculate did you mean value
        $did_you_mean = self::$ignore_did_you_mean === false ? Supasearch_Did_You_Mean::calculate( $query ) : null;

        // If did you mean suggestion found then prepare display text
        if( $did_you_mean !== null ) {
            // Prepare link for ignore corrected term and apply filter
            $url = apply_filters( 'supasearch_misspelling_url', esc_attr( add_query_arg( array( 's' => urlencode( $did_you_mean ), 'idmy' => 1 ), get_bloginfo( 'url' ) ) ) );

            // Get partial HTML output for did you mean text
            $result = Supasearch_Utilities::get_partial( 'did-you-mean', array(
                'prefix'       => $before,
                'link'         => $url,
                'did_you_mean' => $did_you_mean,
                'suffix'       => $after
            ) );
        } else {
            // No did you mean found so set display from settings
            $nothing_found = Supasearch_Utilities::get_option( 'nothing_found' );
            $result = $nothing_found ? $nothing_found : 'Nothing Found';
        }

        // Apply filter to result
        $result = apply_filters( 'supasearch_did_you_mean_suggestion', $result );

        // Print result if echo is set to true
        if( $echo ) {
            echo $result;
        }

        // Return result
        return $result;
    }
}