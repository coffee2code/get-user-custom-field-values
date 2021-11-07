=== Get User Custom Field Values ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: user, custom field, user meta, widget, shortcode, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 5.8
Stable tag: 3.2.2

Use widgets, shortcodes, and/or template tags to easily retrieve and display custom field values for users.

== Description ==

This plugin provides a powerful widget, shortcode (with shortcode builder tool), and template tags for easily retrieving and displaying custom field values for the currently logged in user or any specified user.

This plugin provides functionality similar to the [Get Custom Field Values](https://wordpress.org/plugins/get-custom-field-values/) plugin, but for user custom fields (which WordPress manages in a separate database table).

This plugin does NOT help you in setting user custom field values, nor does it provide an interface to list or otherwise manage user custom fields.

The list of useful user custom field values that are provided by default in WordPress are:

* first_name
* last_name
* nickname
* description
* aim
* yim
* jabber

It is up to other plugins or custom code to add additional user custom fields that you may then be able to retrieve with this plugin.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/get-user-custom-field-values/) | [Plugin Directory Page](https://wordpress.org/plugins/get-user-custom-field-values/) | [GitHub](https://github.com/coffee2code/get-custom-field-values/) | [Author Homepage](https://coffee2code.com)


== Screenshots ==

1. Screenshot of the 'Get User Custom' widget.
2. Screenshot of the 'Get User Custom' shortcode builder (not available in the block editor, aka Gutenberg).


== Installation ==

1. Install via the built-in WordPress plugin installer. Or install the plugin code inside the plugins directory for your site (typically `/wp-content/plugins/`).
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. Optional: Add filters for 'the_user_meta' to filter user custom field data (see the end of the file for commented out samples you may wish to include). And/or add per-meta filters by hooking 'the_user_meta_$field'
4. Give a user a custom field with a value, or have user custom fields already defined. (This generally entails use of plugin(s) that utilize the user custom fields feature built into WordPress. By default, in a practical sense WordPress only sets the 'first_name', 'last_name', and 'nickname' user custom fields, so you could try using one of them, even if just for testing even though WordPress provides functions to get those particular fields.)
5. Optional: Use the provided 'Get User Custom' widget  -or-
Use the available shortcode in a post or page  -or-
Use the function `c2c_get_current_user_custom()` if you wish to access user custom fields for the currently logged
in user. Use the function `c2c_get_user_custom()` to access user custom fields for a specified user. Use the function
`c2c_get_author_custom()` to access custom fields for the current author (when on the permalink page for a post, page, or
in a loop). Prepend either of the three mentioned functions with 'echo' to display the contents of the custom field; or
use the return value as an argument to another function.


== Frequently Asked Questions ==

= How do I assign users custom fields so that I can retrieve them using this plugin? =

The user profile page within WordPress provides inputs for a handful of user custom fields (first_name, last_name, aim, yim, jabber, description, etc). However, you're probably more interested in creating your own user custom fields. In that case, you'll have to use another plugin to store custom fields for users, or directly use WordPress functions manually.

= I don't plan on using the shortcode builder when writing or editing a post or page, so how do I get rid of it? =

If you use the block editor (aka Gutenberg, which is the default editing experience as of WordPress 5.0), then the shortcode builder is not available yet so this situation would be moot for you.

For the classic editor, when on the Write or Edit admin pages for a page or post, find the "Screen Options" link near the upper right-hand corner. Clicking it slides down a panel of options. In the "Show on screen" section, uncheck the checkbox labeled "Get User Custom Field Values - Shortcode". This must be done separately for posts and for pages if you want the shortcode builder disabled for both sections.

= I don't see the shortcode builder; where is it? =

If you use the block editor (aka Gutenberg, which is the default editing experience as of WordPress 5.0), then the shortcode builder is not available yet.

For the classic editor, the shortcode builder/wizard is available in the admin when writing or editing a page or post. On the edit/create page, it'll be a sidebar widget (in this context, also known as a metabox) labeled "Get User Custom Field Values - Shortcode". If you don't see it there (which may be the case since it is hidden by default), find the "Screen Options" link near the upper righthand corner of the page. Clicking it slides down a panel of options. In the "Show on screen" section, check the checkbox labeled "Get User Custom Field Values - Shortcode". This must be done separately for posts and for pages if you want the shortcode builder enabled for both sections.

= Can I move the shortcode builder box because it is way down at the bottom of the right sidebar when I create/edit posts? =

Yes, any of the boxes on the page when creating/editing posts can be rearranged by dragging and dropping the box name. At the very top of the shortcode builder box the cursor will turn into a four-way array indicating you can click to drag that box. You can move it under the post content box, or higher up on the right side.

= Why didn't the shortcode get inserted into the editor after I clicked the "Send shortcode to editor" button? =

Sometimes you have to ensure the text editor has focus. Click within the text editor and make sure the cursor is positioned at the location you want the shortcode to be inserted. Then click the button and the shortcode should get inserted there.

= Is this plugin compatible with the new block editor (aka Gutenberg)? =

Yes, except that the shortcode builder (a custom tool to facilitate making use of the plugin's shortcode when creating a post) has not been ported over yet. The template tags, widget, and shortcode itself all function properly.

= Does this plugin include unit tests? =

Yes.


== Developer Documentation ==

Developer documentation can be found in [DEVELOPER-DOCS.md](https://github.com/coffee2code/get-custom-field-values/blob/master/DEVELOPER-DOCS.md). That documentation covers the numerous template tags, hooks, and shortcode provided by the plugin.

As an overview, these are the template tags provided the plugin:

* `c2c_get_current_user_custom()` : Template tag to get custom fields for the currently logged in user.
* `c2c_get_author_custom()`       : Template tag to get custom fields for the current author (when on the permalink page for a post, page, or in a loop).
* `c2c_get_user_custom()`         : Template tag to get custom fields for a specified user.

These are the hooks provided by the plugin:

* `c2c_get_current_user_custom`                : An alternative approach to safely invoke `c2c_get_current_user_custom()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.
* `c2c_get_author_custom`                      : An alternative approach to safely invoke `c2c_get_author_custom()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.
* `c2c_get_user_custom`                        : An alternative approach to safely invoke `c2c_get_user_custom()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.
* `c2c_get_user_custom_field_values_shortcode` : Filter to customize the name of the plugin's shortcode.
* `c2c_get_user_custom-user_field_proxy`       : Filter to prevent proxying to user object fields if no value for the custom field was found for the user.

The shortcode provided is `[user_custom_field]`, which has a number of attributes to customize its behavior and output.


== Changelog ==

= 3.2.2 (2020-09-18) =
* Change: Restructure unit test file structure
    * New: Create new subdirectory `phpunit/` to house all files related to unit testing
    * Change: Move `bin/` to `phpunit/bin/`
    * Change: Move `tests/bootstrap.php` to `phpunit/`
    * Change: Move `tests/` to `phpunit/tests/`
    * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
* Change: Note compatibility through WP 5.5+

= 3.2.1 (2020-06-07) =
* Change: Update shortcode builder widget to 007:
    * New: Store object instantiated during `register()`
    * Change: Cast return value of `c2c_get_user_custom_field_values_post_types` filter as an array
    * Change: Sanitize string used in markup attributes (hardening)
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and added to it)
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS
* Change: Update link to Get Custom Field Values plugin to point to wordpress.org instead of my site
* Change: Unit tests: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and delete commented-out code)

= 3.2 (2019-12-11) =
* Change: Update widget to 013:
    * Directly load textdomain instead of hooking it to already-fired action
* New: Add README.md
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* Shortcode:
    * Change: Don't show shortcode builder metabox within context of block editor
    * New: Add `show_metabox()`
    * Change: Update version to 006
* Unit tests:
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Tweak whitespace in bootstrap
* Change: Note compatibility through WP 5.3+
* Change: Drop compatibility with version of WP older than 4.6
* Change: Rewrite plugin description
* Change: Update docs to reflect that shortcode builder is not compatible with block editor yet
* Change: Use different markdown formatting for shortcode name to avoid capitalization when displayed in Plugin Directory
* Change: Rename readme.txt section from 'Filters' to 'Hooks'
* Change: Add GitHub link to readme
* Change: Update copyright date (2020)
* Change: Update License URI to be HTTPS

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/get-user-custom-field-values/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 3.2.2 =
Trivial update: Restructured unit test file structure and noted compatibility through WP 5.5+.

= 3.2.1 =
Trivial update: Added TODO.md file, tweaked shortcode builder code, updated a few URLs to be HTTPS, and noted compatibility through WP 5.4+.

= 3.2 =
Minor update: disabled shortcode builder under block editor (it's incompatible), modernized unit tests, noted compatibility through WP 5.3+, dropped compatibility with versions of WP older than 4.6, updated copyright date (2020), and other minor tweaks and documentation improvements

= 3.1 =
Recommended bugfix update: Properly handled serialized meta values, fixed output of wrapping markup in widget if 'id' or 'class' is specified, verified compatibility through WP 4.7+, widget and unit test updates, various fixes and improvements

= 3.0 =
Minor update: improved support for localization, minor unit test tweaks, verified compatibility through WP 4.4+, and updated copyright date (2016)

= 2.9.1 =
Minor bugfix update: Prevented PHP notice under PHP7+ for widget; added more unit tests; updated widget framework to 010; noted compatibility through WP 4.3+

= 2.9 =
Minor update: added more unit tests; updated widget framework to 009; noted compatibility through WP 4.1+; added plugin icon

= 2.8 =
Recommended update: added 'id' and 'class' to widget and shortcode; shortcode handling improvements; added unit tests; noted compatibility through WP 3.8+

= 2.7.1 =
Bug fix update: fix to properly output markup for widget

= 2.7 =
Trivial update: noted compatibility through WP 3.5+; explicitly stated license

= 2.6 =
Recommended update. Highlights: allow shortcode to return user fields (not just custom fields); noted compatibility through WP 3.3+; and more.

= 2.5.1 =
Critical bugfix release (if using shortcode): fixed fatal shortcode bug; minor change to bail out of processing if an empty string is passed a custom field key name

= 2.5 =
Recommended update. Highlights: re-implemented widget based on custom widget framework; localized text; noted compatibility through WP 3.2+; and more.

= 2.0 =
Recommended significant update. Highlights: added widget; added shortcode + shortcode builder; added c2c_get_author_custom(); added multiple hooks to allow customization; verified WP 3.0 compatibility; dropped support for versions of WP older than 2.8; other miscellaneous tweaks and fixes.
