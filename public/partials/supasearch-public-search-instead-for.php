<?php

/**
 * Provides the markup for the 'search instead for' phrase.
 *
 * This file is used to markup the 'search instead for' phrase.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/public/partials
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */

/** @type string $link      The link to search with the query. */
/** @type string $query     The query. */
?>
<p>Search instead for <a href="<?php echo $link; ?>" class="supasearch-query"><?php echo $query; ?></a></p>