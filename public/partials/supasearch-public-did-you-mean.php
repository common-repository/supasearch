<?php

/**
 * Provides the markup for the 'did you mean' phrase.
 *
 * This file is used to markup the 'did you mean' phrase.
 *
 * @since      0.1.0
 * @package    Supasearch
 * @subpackage Supasearch/public/partials
 * @author     David Kane (Supadu) <david.kane@supadu.com>
 */

/** @type string $prefix        The start wrapper of the partial. */
/** @type string $link          The link to search with the new query. */
/** @type string $did_you_mean  The did you mean suggestion. */
/** @type string $suffix        The end wrapper of the partial. */
?>
<?php echo $prefix; ?><a href="<?php echo $link; ?>" class="supasearch-did-you-mean"><?php echo $did_you_mean; ?></a><?php echo $suffix; ?>