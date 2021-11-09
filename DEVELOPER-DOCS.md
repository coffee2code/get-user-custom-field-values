# Developer Documentation

This plugin provides [template tags](#template-tags), a [shortcode](#shortcode), and [hooks](#hooks).


## Template Tags

The plugin provides three optional template tags for use in your theme templates.

### Functions

* `<?php function c2c_get_current_user_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) ?>`
This allows access to custom fields for the currently logged in user. If the current visitor is NOT logged in, then the `$none` value is returned.

* `<?php function c2c_get_author_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) ?>`
This allows access to custom fields for the current author (when on the permalink page for a post, page, or in a loop).

* `<?php function c2c_get_user_custom( $user_id, $field, $before='', $after='', $none='', $between='', $before_last='' ) ?>`
This allows access to custom fields for any user specified by the `$user_id` value.

### Arguments

* `$user_id` _(int)_ :
Required argument, but only applicable for `c2c_get_user_custom()`. The user's ID.

* `$field` _(string)_ :
Required argument. The name of the user custom field to display.

* `$before` _(string)_ :
Optional argument. The text to display before all field value(s).

* `$after` _(string)_ :
Optional argument. The text to display after all field value(s).

* `$none` _(string)_ :
Optional. The text to display in place of the field value should no field value exists; if defined as '' and no field value exists, then nothing (including no `$before` and `$after`) gets displayed.

* `$between` _(string)_ :
Optional argument. The text to display between multiple occurrences of the custom field; if defined as '', then only the first instance will be used.

* `$before_last` _(string)_ :
Optional argument. The text to display between the next-to-last and last items listed when multiple occurrences of the custom field exist; `$between` MUST be set to something other than '' for this to take effect.

#### Examples

* `<?php c2c_get_current_user_custom('first_name'); ?>`
"Scott"

* `<?php c2c_get_current_user_custom('favorite_colors', 'Favorite colors: '); /* Where the 'favorite_colors' user custom field has been defined with values ?>`
Example output: "Favorite colors: blue, gray, green, black, red"

* `<?php c2c_get_current_user_custom('favorite_colors', 'Favorite colors: <ul><li>', '</li></ul>', '', '</li><li>'); ?>`
Example output: "Favorite colors: <ul><li>blue</li><li>gray</li><li>green</li><li>black</li><li>red</li></ul>"

* `<?php echo c2c_get_user_custom(3, 'first_name', 'Hi, ', '. Welcome back.'); // where 3 is the id of the user we want ?>`
Example output: "Hi, Scott. Welcome back."


## Shortcode

This plugin provides one shortcode that can be used within the body of a post or page and wherever else shortcodes are supported. The shortcode is accompanied by a shortcode builder (see Screenshots) that presents a form for easily creating a shortcode. However, here's the documentation for the shortcode and its supported attributes.

The name of the shortcode can be changed via the filter `c2c_get_user_custom_field_values_shortcode` (though making this customization is only recommended for before your first use of the shortcode, since changing to a new name will cause the shortcodes previously defined using the older name to no longer work).

Note: this plugin's shortcode is only available for use by authors with the ability to post scripts (aka the 'unfiltered_html' capability), such as those with the editor or administrator role (except on Multisite) or the super administrator role. For authors without that capability (such as contributors and authors), the shortcode builder is not available and any instances of the shortcode in the post are ignored. See documentation for the `get_user_custom_field_values/can_author_use_shortcodes` to customize this behavior.

### `user_custom_field`

The only shortcode provided by this plugin is named `user_custom_field`. It is a self-closing tag, meaning that it is not meant to encapsulate text. Except for 'field', all attributes are optional, though you'll likely need to provide a couple to achieve your desired result.

#### Attributes

* **field**       : _(string)_ The name of the user custom field key whose value you wish to have displayed.
* **id**          : _(string)_ The text to use as the 'id' attribute for a 'span' tag that wraps the output
* **class**       : _(string)_ The text to use as the 'class' attribute for a 'span' tag that wraps the output
* **this_post**   : _(boolean)_ Get the custom field value for the author of the post containing this shortcode? Takes precedence over user_id attribute. Specify `1` (for true) or `0` for false. Default is `0`.
* **user_id**     : _(integer)_ ID of user whose custom field's value you want to display. Leave blank to search for the custom field for the currently logged in user. Use `0` to indicate it should only work on the permalink page for a page/post.
* **before**      : _(string)_ Text to display before the custom field.
* **after**       : _(string)_ Text to display after the custom field.
* **none**        : _(string)_ Text to display if no matching custom field is found (or it has no value). Leave this blank if you don't want anything to display when no match is found.
* **between**     : _(string)_ Text to display between custom field items if more than one are being shown. Default is ', '.
* **before_last** : _(string)_ Text to display between the second to last and last custom field items if more than one are being shown.

#### Examples

* Get nickname for current post's author
`[user_custom_field field="nickname" this_post="1" /]`

* Get AIM account name for a specific user
`[user_custom_field field="aim" user_id="2" /]`

* Wrap post author's bio in markup, but only if the author has a bio.
`[user_custom_field field="description" before="My bio:" /]`


## Hooks

The plugin exposes five filters for hooking. Code using these filters should ideally be put into a mu-plugin or site-specific plugin (which is beyond the scope of this readme to explain). Less ideally, you could put them in your active theme's functions.php file.

### `c2c_get_current_user_custom` _(filter)_

The `c2c_get_current_user_custom` hook allows you to use an alternative approach to safely invoke `c2c_get_current_user_custom()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

#### Arguments

* same as for `c2c_get_current_user_custom()`

#### Example

Instead of:

`<?php $twitter = c2c_get_current_user_custom( 'twitter' ); ?>`

Do:

`<?php $twitter = apply_filters( 'c2c_get_current_user_custom', 'twitter' ); ?>`

### `c2c_get_author_custom` _(filter)_

The `c2c_get_author_custom` hook allows you to use an alternative approach to safely invoke `c2c_get_author_custom()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

#### Arguments

* same as for `c2c_get_author_custom()`

#### Example

Instead of:

`<?php $aim = c2c_get_author_custom( 'aim', 'AIM: ' ); ?>`

Do:

`<?php $aim = apply_filters( 'c2c_get_author_custom', 'aim', 'AIM: ' ); ?>`

### `c2c_get_user_custom` _(filter)_

The `c2c_get_user_custom` hook allows you to use an alternative approach to safely invoke `c2c_get_user_custom()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

#### Arguments

* same as for `c2c_get_user_custom()`

#### Example

Instead of:

`<?php $address = c2c_get_user_custom( 5, 'address' ); ?>`

Do:

`<?php $address = apply_filters( 'c2c_get_user_custom', 5, 'address ); ?>`

### `c2c_get_user_custom_field_values_shortcode` _(filter)_

The `c2c_get_user_custom_field_values_shortcode` hook allows you to define an alternative to the default shortcode tag. By default the shortcode tag name used is 'user_custom_field'. It is recommended you only utilize this filter before making use of the plugin's shortcode in posts and pages. If you change the shortcode tag name, then any existing shortcodes using an older name will no longer work (unless you employ further coding efforts).

#### Arguments

* **$shortcode** _(string)_ :
The name for the shortcode to be handled by this plugin. Default is 'user_custom'. If you opt to change this, you should do so prior to first use of the plugin's shortcode. Once changed, the plugin will no longer recognize any pre-existing shortcodes using the default name.


#### Example

```php
// Use a shorter shortcode: i.e. [ucf field="last_name" /]
add_filter( 'c2c_get_user_custom_field_values_shortcode', 'change_c2c_get_user_custom_field_values_shortcode' );
function change_c2c_get_user_custom_field_values_shortcode( $shortcode ) {
	return 'ucf';
}
```

### `c2c_get_user_custom-user_field_proxy` _(filter)_

The `c2c_get_user_custom-user_field_proxy` hook allows you to prevent proxying to user object fields if no custom value for the custom field was found for the user. By default, if a user does not have a value for the given custom field, the plugin will compare the field name to the small list of user object fields (i.e. user table fields) to see if it is a valid user field. If so, then the user field value (as opposed to custom field value) is returned.

#### Arguments

* **$allow_proxy** _(boolean)_ Default of true.

#### Example:

```php
// Prevent user field proxying: i.e. this would not return anything: [user_custom_field field="user_email" user_id="1" /]
add_filter( 'c2c_get_user_custom-user_field_proxy', '__return_false' );
```

### `get_user_custom_field_values/can_author_use_shortcodes` _(filter)_

The `get_user_custom_field_values/can_author_use_shortcodes` filter allows you to override whether a post author is able to use the shortcode provided by the plugin. By default, the plugin's shortcode is only available for use by authors with the ability to post scripts (aka the 'unfiltered_html' capability), such as those with the editor or administrator role (except on Multisite) or the super administrator role. The limitation exists to prevent potential disclosure of potentially private information stored in user meta.

#### Arguments

* `$can` _(boolean)_ :
Whether or not the post author can use the 'user_custom_field' shortcode, as determined by `can_author_use_shortcodes()`.

* `$user` _(WP\_User|false)_ :
The user.

* `$post` _(WP\_Post|false)_ :
The post

#### Example

```php
// Allow authors, regardless of capabilities, to use the 'user_custom_field' shortcode.
add_filter( 'get_user_custom_field_values/can_author_use_shortcodes', '__return_true' );
```
