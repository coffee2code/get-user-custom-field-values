# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Create hooks to allow disabling shortcode, shortcode builder, and widget support
* Support getting random custom field values
* Support specifying a limit on the number of custom field values returned
* Facilitate conditional output, maybe via `c2c_get_user_custom_if()` where text is only output if post
  has the custom field AND it equals a specified value (or one of an array of possible values)
  `echo c2c_get_user_custom_if( 'size', array( 'XL', 'XXL' ), 'Sorry, this size is out of stock.' );`
* Introduce a 'format' shortcode attribute and template tag argument.  Defines the output format for each matching custom field,
  i.e. `c2c_get_user_custom(..., $format = 'Size %key% has %value%' in stock.')`
* Support specifying $field as array or comma-separated list of custom fields
* Create args array alternative template tag: `c2c_user_custom_field( $field, $args = array() )` so features can be added and multiple arguments don't have to be explicitly provided.
  Perhaps transition `c2c_get_user_custom()` in plugin's v3.0 and detect args.
  ```
  function c2c_get_user_custom( $field, $args = array() ) {
     if ( ! empty( $args ) && ! is_array( $args ) ) // Old style usage
       return c2c_old_get_user_custom( $field, ... ); // Or: $args = c2c_get_user_custom_args_into_array( ... );
     // Do new handling here.
   }
  ```
* Support name filters to run against found custom fields
  `c2c_get_user_custom( 'favorite_site', array( 'filters' => array( 'strtoupper', 'make_clickable' ) ) )`
* Since it's shifting to args array, might as well support 'echo'
* Allow $field value to actually be an array of different field names to use.
  See: https://wordpress.org/support/topic/multiple-field-output-in-widget

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/get-user-custom-field-values/) or on [GitHub](https://github.com/coffee2code/get-user-custom-field-values/) as an issue or PR).