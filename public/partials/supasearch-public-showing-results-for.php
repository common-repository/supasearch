<?php

/**
 * Provides the markup for the 'showing results for' phrase.
 *
 * This file is used to markup the 'showing results for' phrase.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/public/partials
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */

/** @type string $prefix    The start wrapper of the partial. */
/** @type string $link      The link to search with the new query. */
/** @type string $query     The new query. */
/** @type string $suffix    The end wrapper of the partial. */
?>
<?php echo $prefix; ?><a href="<?php echo $link; ?>" class="supasearch-misspelling"><?php echo $query; ?></a><?php echo $suffix; ?>