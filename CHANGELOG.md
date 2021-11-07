# Changelog

## _(in-progress)_
* Change: Note compatibility through WP 5.8+
* Change: Update copyright date (2021)
* Unit tests:
    * Change: Restructure unit test file structure
        * Change: Move `phpunit/bin/` to `tests/bin/`
        * Change: Move `phpunit/bootstrap.php` into `tests/phpunit/`
        * Change: Move tests from `phpunit/tests/` to `tests/phpunit/tests/`
        * Change: Remove 'test-' prefix from unit test files

## 3.2.2 _(2020-09-18)_
* Change: Restructure unit test file structure
    * New: Create new subdirectory `phpunit/` to house all files related to unit testing
    * Change: Move `bin/` to `phpunit/bin/`
    * Change: Move `tests/bootstrap.php` to `phpunit/`
    * Change: Move `tests/` to `phpunit/tests/`
    * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
* Change: Note compatibility through WP 5.5+

## 3.2.1 _(2020-06-07)_
* Change: Update shortcode builder widget to 007:
    * New: Store object instantiated during `register()`
    * Change: Cast return value of `c2c_get_user_custom_field_values_post_types` filter as an array
    * Change: Sanitize string used in markup attributes (hardening)
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and added to it)
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS
* Change: Update link to Get Custom Field Values plugin to point to wordpress.org instead of my site
* Change: Unit tests: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and delete commented-out code)

## 3.2 _(2019-12-11)_
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

## 3.1 _(2017-03-14)_
* Fix: Properly handle serialized meta values
* Fix: Properly sanitize field name prior so use as part of a hook name
* Change: Update widget to 012:
    * Correctly pass `$before` and `$after` args in call to `c2c_get_author_custom()`
    * Fix conditional check to properly wrap custom field in span when 'id' and/or 'class' is specified
    * Add `register_widget()` and change to calling it when hooking 'admin_init'
    * Load textdomain
    * Add more substantial unit tests
* Change: Update widget framework:
    * 013:
    * Add `get_config()` as a getter for config array
    * 012:
    * Go back to non-plugin-specific class name of c2c_Widget_012
    * Don't load textdomain
    * Declare class and `load_config()` and `widget_body()` as being abstract
    * Change class variable `$config` from public to protected
    * Discontinue use of `extract()`
    * Apply 'widget_title' filter to widget title
    * Add more inline documentation
    * Minor code reformatting (spacing, bracing, Yoda-ify conditions)
* Change: Update shortcode builder widget to 005:
    * Use `get_config()` to get widget config now that the object variable is protected
    * Add `register()` and change to calling it when hooking 'init'
    * Add more unit tests
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Use officially documented order of arguments for `implode()`
* Change: Ensure `$authordata` exists before using its valie (more hardening than a fix)
* Change: Rephrase conditions to omit unnecessary use of `empty()`
* Change: Modify plugin description
* Change: Tweak readme.txt (minor content changes, spacing)
* Change: Note compatibility through WP 4.7+
* Change: Update copyright date (2017)
* New: Add LICENSE file.

## 3.0 _(2016-02-01)_
* Change: Update widget framework to 011:
    * Change class name to `c2c_GetUserCustomFieldValues_Widget_011` to be plugin-specific.
    * Set textdomain using a string instead of a variable.
    * Remove `load_textdomain()` and textdomain class variable.
    * Formatting improvements to inline docs.
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Don't load textdomain from file.
    * Remove .pot file and /lang subdirectory.
    * Remove 'Domain Path' from plugin header.
    * Add 'Text Domain' to plugin header.
* Change: Reformat widget settings code (spacing).
* Change: Explicitly declare methods in unit tests as public.
* Change: Minor improvements to inline docs and test docs.
* New: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Note compatibility through WP 4.4+.
* Change: Update copyright date (2016).

## 2.9.1 _(2015-08-21)_
* Change: Discontinue use of PHP4-style constructor invocation of WP_Widget to prevent PHP notices in PHP7.
* Change: Use `require_once()` instead of `include()` for including include files.
* Change: Use full path to include files.
* Change: Update widget framework to version 010.
* Change: Update widget to version 009.
* Change: Update shortcode to version 003.
* Change: Note compatibility through WP 4.3+.
* New: Add unit tests for shortcode and widget class versions.
* New: Add `c2c_GetUserCustomWidget::version()` to get version of the widget class.
* New: Add `c2c_GetUserCustomFieldValuesShortcode::version()` to get version of the shortcode class.

## 2.9 _(2015-03-04)_
* Update widget framework to 009
* Update widget to 008
* Explicitly declare widget class methods public
* Add more unit tests
* Reformat plugin header
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Minor documentation improvements and spacing changes throughout
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Add plugin icon
* Regenerate .pot

## 2.8 _(2014-01-17)_
* Hide shortcode wizard by default (won't change existing setting for users)
* Show shortcode wizard for new posts as well
* Add 'id' and 'class' as shortcode and widget attributes to set same-named HTML attributes on 'span' tag
* Wrap output in 'span' tag if either 'id' or 'class' shortcode/widget attribute is defined
* Add unit tests
* Cast all intended integer arguments as `absint()` instead of `intval()`
* Update widget version to 007
* Update widget framework to 008
* Use explicit path for `require_once()`
* Discontinue use of PHP4-style constructor
* Minor documentation improvements
* Minor code reformatting (spacing, bracing)
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.6
* Update copyright date (2014)
* Regenerate .pot
* Change donate link
* Update screenshots
* Add banner

## 2.7.1
* Change `widget_body()` to return widget content instead of echoing it, to fix widget display
* Update widget version to 006

## 2.7
* Add check to prevent execution of code if file is directly accessed
* Update widget version to 005
* Update widget framework to 007
    * Prevent output of anything if there is no widget_body() output
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Add 'Domain Path' directive to top of main plugin file
* Discontinue use of explicit pass-by-reference for objects
* Remove ending PHP close tag
* Note compatibility through WP 3.5+
* Update copyright date (2013)
* Move screenshots into repo's assets directory

## 2.6
* Attempt to return user object field value if no custom fields values found and field name matches user object field name
* Add filter `c2c_get_user_custom-user_field_proxy` to prevent accessing user object fields
* Trim user inputs for widget (and intval user_id) during validation
* Note compatibility through WP 3.3
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

## 2.5.1
* Fix fatal shortcode bug by updating widget framework to v005 to make a protected class variable public
* Return immediately in `c2c_get_user_custom()` if value of $field is empty string
* Update widget version to 003

## 2.5
* Use `get_user_meta()` if defined (WP3.0+), rather than direct SQL query
* Use real functions rather than `create_function()` to register widget and shortcode
* Re-implemented widget, basing it on widget framework v004
* Document shortcode
* Rename widget class from `GetUserCustomWidget` to `c2c_GetUserCustomWidget`
* Add filter `c2c_get_user_custom_field_values_shortcode` to allow changing shortcode name
* Rename shortcode class from `GetUserCustomFieldValuesShortcode` to `c2c_GetUserCustomFieldValuesShortcode`
* Add screenshots
* Add .pot
* Change extended description
* Minor code formatting changes (spacing)
* Note compatibility through WP 3.2+
* Update copyright date (2011)

## 2.0 _(not publicly released)_
* Add hooks `c2c_get_current_user_custom` (filter), `c2c_get_author_custom` (filter), and `c2c_get_user_custom` (filter) to respond to the function of the same name so that users can use the `apply_filters()` notation for invoking template tags
* Wrap each global function in `if(!function_exists())` check
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Note compatibility with WP 2.9+, 3.0+
* Drop compatibility with versions of WP older than 2.8
* Minor tweaks to code formatting (spacing)
* Add Filters, Screenshots, and Upgrade Notice sections to readme.txt
* Add PHPDoc documentation
* Add package info to top of plugin file
* Update copyright date (2010)
* Remove trailing whitespace

## 1.5 _(unreleased)_
* Add widget with full support of all capabilities of plugin
* Add shortcode with full support of all capabilities of plugin
* Add `c2c_get_author_custom()` to access custom fields for the current author (when on the permalink page for a post/page, or in a loop)
* Fix inability to list multiple same-named custom fields that resulted due to changed behavior in WP
* Note compatibility through WP2.8+
* Remove compatibility with versions of WP older than 2.6
* Minor formatting tweaks
* Add Changelog to readme.txt
* Update copyright date

## 1.0.1
* Minor bugfix

## 1.0
* Initial release
