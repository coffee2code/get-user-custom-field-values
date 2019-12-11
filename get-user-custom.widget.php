<?php
/**
 * Get User Custom Field Values plugin widget code.
 *
 * Copyright (c) 2004-2020 by Scott Reilly (aka coffee2code)
 *
 * @package c2c_GetUserCustomWidget
 * @author  Scott Reilly
 * @version 012
 */

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_GetUserCustomWidget' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-widget.php' );

class c2c_GetUserCustomWidget extends c2c_Widget_013 {

	/**
	 * Returns version of the widget.
	 *
	 * @since 009
	 *
	 * @return string
	 */
	public static function version() {
		return '012';
	}

	/**
	 * Registers the widget.
	 *
	 * @since 012
	 */
	public static function register_widget() {
		register_widget( __CLASS__ );
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'get-user-custom', __FILE__, array( 'width' => 300 ) );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_filter( $this->get_hook( 'excluded_form_options' ), array( $this, 'excluded_form_options' ) );
	}

	/**
	 * Loads the plugin textdomain.
	 *
	 * @since 012
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'get-user-custom-field-values' );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	public function load_config() {
		$this->title       = __( 'Get User Custom Field', 'get-user-custom-field-values' );
		$this->description = __( 'A list of custom field value(s) from user(s).', 'get-user-custom-field-values' );

		$this->config = array(
			// input can be 'checkbox', 'multiselect', 'select', 'short_text', 'text', 'textarea', 'hidden', or 'none'
			// datatype can be 'array' or 'hash'
			// can also specify input_attributes
			'title' => array(
				'input'   => 'text',
				'default' => __( 'User Custom Field', 'get-user-custom-field-values' ),
				'label'   => __( 'Title', 'get-user-custom-field-values' ),
			),
			'field' => array(
				'input'   => 'text',
				'default' => '',
				'label'   => __( 'Custom field key', 'get-user-custom-field-values' ),
				'help'    => __( '<strong>*Required.</strong>  The name of the custom field key whose value you wish to have displayed.', 'get-user-custom-field-values' ),
			),
			'this_post' => array(
				'input'   => 'checkbox',
				'default' => false,
				'label'   => __( 'This post\'s author?', 'get-user-custom-field-values' ),
				'help'    => __( 'For the author of the post containing this shortcode. Takes precedence over \'User ID\'', 'get-user-custom-field-values' ),
			),
			'user_id' => array(
				'input'   => 'short_text',
				'default' => '',
				'label'   => __( 'User ID', 'get-user-custom-field-values' ),
				'help'    => __( 'ID of user whose custom field\'s value you want to display. Leave blank to search for the custom field for the currently logged in user. Use <code>0</code> to indicate it should only work on the permalink page for a page/post.', 'get-user-custom-field-values' ),
			),
			'before' => array(
				'input' => 'text',
				'default' => '',
				'label'   => __( 'Before text', 'get-user-custom-field-values' ),
				'help'    => __( 'Text to display before the custom field.', 'get-user-custom-field-values' ),
			),
			'after' => array(
				'input'   => 'text',
				'default' => '',
				'label'   => __( 'After text', 'get-user-custom-field-values' ),
				'help'    => __( 'Text to display after the custom field.', 'get-user-custom-field-values' ),
			),
			'none' => array(
				'input'   => 'text',
				'default' => '',
				'label'   => __( 'None text', 'get-user-custom-field-values' ),
				'help'    => __( 'Text to display if no matching custom field is found (or it has no value). Leave this blank if you don\'t want anything to display when no match is found.', 'get-user-custom-field-values' ),
			),
			'between' => array(
				'input'   => 'text',
				'default' => ', ',
				'label'   => __( 'Between text', 'get-user-custom-field-values' ),
				'help'    => __( 'Text to display between custom field items if more than one are being shown.', 'get-user-custom-field-values' ),
			),
			'before_last' => array(
				'input'   => 'text',
				'default' => '',
				'label'   => __( 'Before last text', 'get-user-custom-field-values' ),
				'help'    => __( 'Text to display between the second to last and last custom field items if more than one are being shown.', 'get-user-custom-field-values' ),
			),
			'id' => array(
				'input'   => 'text',
				'default' => '',
				'label'   => __( 'HTML id', 'get-user-custom-field-values' ),
				'help'    => __( 'The \'id\' attribute for the &lt;span&gt; tag to surrounds output.', 'get-user-custom-field-values' ),
			),
			'class' => array(
				'input'   => 'text',
				'default' => '',
				'label'   => __( 'HTML class', 'get-user-custom-field-values' ),
				'help'    => __( 'The \'class\' attribute for the &lt;span&gt; tag to surrounds output.', 'get-user-custom-field-values' ),
			),
		);
	}

	/**
	 * Outputs the body of the widget.
	 *
	 * @param array $args Widget args.
	 * @param array $instance Widget instance.
	 * @param array $settings Widget settings.
	 */
	public function widget_body( $args, $instance, $settings ) {
		extract( $args );
		extract( $settings );

		$ret = '';

		// Determine, based on inputs given, which template tag to use.
		if ( 0 === $user_id || $this_post ) {
			$user_id = 'current';
		}

		if ( $user_id ) {
			if ( 'current' == $user_id ) {
				$ret = c2c_get_author_custom( $field, $before, $after, $none, $between, $before_last );
			} else {
				$ret = c2c_get_user_custom( $user_id, $field, $before, $after, $none, $between, $before_last );
			}
		} else {
			$ret = c2c_get_current_user_custom( $field, $before, $after, $none, $between, $before_last );
		}

		// If either 'id' or 'class' attribute was defined, then wrap output in span
		if ( $ret && ( $id || $class ) ) {
			$tag = '<span';

			if ( $id ) {
				$tag .= ' id="' . esc_attr( $id ) . '"';
			}

			if ( $class ) {
				$tag .= ' class="' . esc_attr( $class ) . '"';
			}

			$tag .= ">$ret</span>";
			$ret = $tag;
		}

		return $ret;
	}

	/**
	 * Validates widget instance values.
	 *
	 * @param array  $instance Widget instance values.
	 * @return array The filtered widget instance values.
	 */
	public function validate( $instance ) {
		$instance['field']   = trim( $instance['field'] );
		$instance['user_id'] = trim( $instance['user_id'] );
		if ( $instance['user_id'] ) {
			$instance['user_id'] = intval( $instance['user_id'] );
		}

		return $instance;
	}

	/**
	 * Defines widget form options that shouldn't be shown by default (since they are used for the shortcode widget).
	 *
	 * @param array  $excluded_form_options Array of form options that shouldn't be shown.
	 * @return array The array of form options that shouldn't be shown.
	 */
	public function excluded_form_options( $excluded_form_options ) {
		if ( $excluded_form_options === null ) {
			$excluded_form_options = array( 'this_post' );
		}

		return $excluded_form_options;
	}

} // end class c2c_GetUserCustomWidget

add_action( 'widgets_init', array( 'c2c_GetUserCustomWidget', 'register_widget' ) );

endif; // end if !class_exists()
