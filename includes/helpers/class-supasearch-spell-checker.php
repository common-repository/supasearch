<?php

/**
 * The spell checker
 *
 * Implements type correct based on phonetics of words using a dictionary of words extracted from the database.
 *
 * Based on the concepts of Felipe Ribeiro:
 * https://github.com/machinaut/book-checker/blob/master/norvig/SpellCorrector.php
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/includes/helpers
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */
class Supasearch_Spell_Checker {
    /**
     * Generates a list of possible "disturbances" on the passed string
     *
     * @param string $word
     *
     * @return array
     */
    private static function edits1( $word ) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $alphabet = str_split( $alphabet );
        $n = strlen( $word );
        $edits = array();
        for( $i = 0; $i < $n; $i++ ) {
            $edits[] = substr( $word, 0, $i ) . substr( $word, $i + 1 );        //deleting one char
            foreach( $alphabet as $c ) {
                $edits[] = substr( $word, 0, $i ) . $c . substr( $word, $i + 1 ); //substituting one char
            }
        }
        for( $i = 0; $i < $n - 1; $i++ ) {
            $edits[] = substr( $word, 0, $i ) . $word[$i + 1] . $word[$i] . substr( $word, $i + 2 ); //swapping chars order
        }
        for( $i = 0; $i < $n + 1; $i++ ) {
            foreach( $alphabet as $c ) {
                $edits[] = substr( $word, 0, $i ) . $c . substr( $word, $i ); //inserting one char
            }
        }

        return $edits;
    }

    private function compare_count( $a, $b ) {
        return strcmp( $b->count, $a->count );
    }

    /**
     * Generate possible "disturbances" in a second level that exist on the dictionary
     *
     * @param string $word
     *
     * @return array
     */
    private static function known_edits2( $word ) {
        $known = array();
        $words = Supasearch_Dictionary::get_all( OBJECT_K );

        foreach( array_unique( self::edits1( $word ) ) as $e1 ) {
            $known = array_merge( $known, array_intersect_key( $words, array_flip( array_unique( self::edits1( $e1 ) ) ) ) );
        }

        usort( $known, array( 'self', 'compare_count' ) );

        return $known;
    }

    private static function correction_known( $word ) {
        $known = Supasearch_Known::get( $word );

        // Return the correction if its found
        if( $known !== null ) {
            /** @type stdClass $known */
            $known = $known->correction;
        }

        return $known;
    }

    /**
     * Given a list of words, returns the subset that is present on the dictionary
     *
     * @param array $words
     *
     * @return array
     */
    private static function known( array $words ) {
        if( empty( $words ) ) {
            // No words provided to return
            return $words;
        } elseif( count( $words ) === 1 ) {
            // Get specific word
            $known = Supasearch_Dictionary::get( $words[0] );
        } else {
            // Get based on list of edits
            $known = Supasearch_Dictionary::get( $words, '%s IN (' . implode( ', ', array_map( function() {
                    return '%%s';
                }, array_keys( $words ) ) ) . ')' );
        }

        return $known;
    }


    public static function get_closest_phonetically( $corrections, $word, $min_match_percentage = 0, $min_log_count = 0 ) {
        // Get list of expletive words
        $expletives = Supasearch_Utilities::get_expletive_words();

        // Check with corrects is populated array
        if( empty( $corrections ) ) {
            return $word;
        } elseif( is_object( $corrections ) ) {
            return $corrections->word !== null && !in_array( $corrections->word, $expletives ) ? $corrections->word : $word;
        }

        // Set metrics
        $shortest = -1;
        $closest = array();
        $min_match_percentage = (float) $min_match_percentage;
        $percentage_check = $min_match_percentage > 0 ? true : false;

        // Loop potentional corrections
        foreach( $corrections as $k ) {
            // Skip expletive words
            if( in_array( $k->word, $expletives ) ) {
                continue;
            }

            // Get levenshtein value of word phonetics
            $lev = levenshtein( metaphone( $word ), metaphone( $k->word ) );

            // Calculate lev percentage
            $lev_percentage = $percentage_check ? ( 1 - ( $lev / max( strlen( metaphone( $word ) ), strlen( metaphone( $k->word ) ) ) ) ) : $lev;

            // Store the closest value if above a match percentage
            if( ( $lev <= $shortest || $shortest < 0 ) && $lev_percentage >= $min_match_percentage && (int) $k->count >= $min_log_count ) {
                $closest[$k->word] = (int) $k->count;
                $shortest = $lev;
            }
        }

        // Return the closest with the highest count
        return empty( $closest ) ? $word : array_shift( array_keys( $closest, max( $closest ) ) );
    }

    /**
     * Returns the word that is present on the dictionary that is the most similar (and the most relevant) to the
     * word passed as parameter,
     *
     * @param string $word
     *
     * @return string
     */
    public static function correct( $word ) {
        $word = trim( strtolower( $word ) );

        // Validate word is set
        if( empty( $word ) ) {
            return false;
        }

        // Calculate correction
        if( $correction = self::correction_known( $word ) ) {
            return $correction;
        } elseif( self::known( array( $word ) ) ) {
            return $word;
        } elseif( ( $corrections = self::known( self::edits1( $word ) ) ) ) {
            $corrected_word = self::get_closest_phonetically( $corrections, $word );
        } elseif( ( $corrections = self::known_edits2( $word ) ) ) {
            $corrected_word = self::get_closest_phonetically( $corrections, $word );
        } else {
            $corrected_word = $word;
        }

        // Insert correction so it's remembered
        Supasearch_Known::replace( array(
            'word'       => $word,
            'correction' => $corrected_word
        ) );

        // Return correction
        return $corrected_word;
    }

    /**
     * Retains the relevant casing of the original word
     *
     * @param string $word           Original misspelt word
     * @param string $corrected_word New word which has been corrected
     *
     * @return string
     */
    public static function casing( $word, $corrected_word ) {
        // Maintain casing on correct word
        if( ctype_upper( $word ) ) {
            return strtoupper( $corrected_word );
        } elseif( preg_match( '/^[A-Z]{1}/', $word ) ) {
            return ucwords( $corrected_word );
        }

        return $corrected_word;
    }

    /**
     * Checks each word of the term passed as parameter and returns the word that is present on the dictionary that is
     * the most similar (and the most relevant)
     *
     * @param string $term Searched term
     *
     * @return string
     */
    public static function check( $term ) {
        $correctedTerm = array();

        // Split term into individual words to correct
        foreach( explode( ' ', $term ) as $word ) {
            $correctedTerm[] = self::casing( $word, self::correct( $word ) );
        }

        // Return corrected term
        return implode( ' ', $correctedTerm );
    }
}