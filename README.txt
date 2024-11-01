=== Plugin Name ===
Contributors: david.kane
Donate link: http://www.supadu.com
Tags: supadu, search, misspellings, did you mean,
Requires at least: 4.0
Tested up to: 4.6.1
Stable tag: 0.2.0-beta
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Supasearch enhances the default WordPress search.

== Description ==

Supasearch enhances the default WordPress search.

The [Supasearch plugin](http://www.supadu.com/supasearch/) allows users to quickly and easily enhance the default search provided by WordPress. Just install and activate this plugin to have an immediately improved site search.

**Features include:**

* **Misspellings.** If no results are found for a search term the plugin which check if the word is misspelt and provide results for the corrected word.
* **Did you mean?.** If no results are found the plugin will offer a similar alternative for the user to search against.

== Installation ==

1. Upload `supasearch` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php if( class_exists( 'Supasearch_Public' ) ) { Supasearch_Public::misspelling( get_search_query(), 'Showing results for <span>', '</span>' ); } ?>` in your search results template
4. Place `<?php if( class_exists( 'Supasearch_Public' ) ) { Supasearch_Public::did_you_mean( get_search_query(), 'Did you mean: <span>', '</span>' ); } ?>` in your search results template "Nothing Found" section

== Frequently Asked Questions ==

= How to I display the misspelling feature in my theme? =

Place the following code in your search results template, typically the `search.php` file within your theme:

`<?php if( class_exists( 'Supasearch_Public' ) ) { Supasearch_Public::misspelling( get_search_query(), 'Showing results for <span>', '</span>' ); } ?>`

= What about foo bar? =

Place the following code in your template which renders when nothing is found, typically the `template-parts/content-none.php` file within your theme:

`<?php if( class_exists( 'Supasearch_Public' ) ) { Supasearch_Public::did_you_mean( get_search_query(), 'Did you mean: <span>', '</span>' ); } ?>`

== Screenshots ==

1. Add one simple line of code to you theme to enable intelligent type correction.
2. Sync the content of your site to have more relevant suggestions.

== Changelog ==

= 0.2.0-beta =
Release Date: October 31, 2016

* Correction logic update to use previous popular queries.
* Settings added to control accuracy previous popular queries.

= 0.1.0-beta =
Release Date: October 17, 2016

* Beta release featuring misspellings and did you mean suggestions to enhance the default WordPress search.

== Upgrade Notice ==

= 0.2.0-beta =
This BETA release is intended for testing and feedback only and may change without notice.