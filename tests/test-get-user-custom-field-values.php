<?php

defined( 'ABSPATH' ) or die();

class Get_User_Custom_Field_Values_Test extends WP_UnitTestCase {

	public function tearDown() {
		parent::tearDown();
		$this->unset_current_user();
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	private function create_user_with_meta( $metas = array(), $user_data = array() ) {
		$user_id = $this->factory->user->create( $user_data );

		if ( empty( $metas ) ) {
			$metas = $this->get_sample_metas();
		}

		foreach ( $metas as $key => $val ) {
			if ( is_array( $val ) ) {
				foreach ( $val as $v ) {
					add_user_meta( $user_id, $key, $v );
				}
			} else {
				add_user_meta( $user_id, $key, $val );
			}
		}

		return $user_id;
	}

	/**
	 * Unsets current user globally. Taken from post.php test.
	 */
	private function unset_current_user() {
		global $current_user, $user_ID;

		$current_user = $user_ID = null;
    }

	private function get_sample_metas() {
		return array(
			'mood'  => 'happy',
			'child' => array( 'adam', 'bob', 'cerise', 'diane' ),
			'color' => array( 'blue', 'white' ),
			'tshirt size' => 'M',
			'location' => 'Denver, CO',
		);
	}


	//
	//
	// TESTS
	//
	//


	/* c2c_get_current_user_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) */

	public function test_c2c_get_current_user_custom_with_field() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'happy',      c2c_get_current_user_custom( 'mood' ) );
		$this->assertEquals( 'Denver, CO', c2c_get_current_user_custom( 'location' ) );
		$this->assertEmpty( c2c_get_current_user_custom( 'nonexistent' ) );
	}

	public function test_c2c_get_current_user_custom_with_before() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Mood: happy', c2c_get_current_user_custom( 'mood', 'Mood: ' ) );
		$this->assertEmpty( c2c_get_current_user_custom( 'nonexistent', 'Mood: ' ) );
	}

	public function test_c2c_get_current_user_custom_with_after() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Mood: happy!', c2c_get_current_user_custom( 'mood', 'Mood: ', '!' ) );
		$this->assertEmpty( c2c_get_current_user_custom( 'nonexistent', 'Mood: ', '!' ) );
	}

	public function test_c2c_get_current_user_custom_with_none() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Mood: happy!',   c2c_get_current_user_custom( 'mood', 'Mood: ', '!', 'unknown' ) );
		$this->assertEquals( 'Mood: unknown!', c2c_get_current_user_custom( 'nonexistent', 'Mood: ', '!', 'unknown' ) );
	}

	public function test_c2c_get_current_user_custom_with_between_for_single_value() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Mood: happy!', c2c_get_current_user_custom( 'mood', 'Mood: ', '!', 'unknown', ', ' ) );
		$this->assertEmpty( c2c_get_current_user_custom( 'nonexistent', 'Mood: ', '!', '', ', ' ) );
	}

	public function test_c2c_get_current_user_custom_with_between_for_two_values() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Colors: blue, white.', c2c_get_current_user_custom( 'color', 'Colors: ', '.', 'none', ', ' ) );
	}

	public function test_c2c_get_current_user_custom_with_between_for_multiple_values() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Children: adam, bob, cerise, diane.', c2c_get_current_user_custom( 'child', 'Children: ', '.', 'none', ', ' ) );
	}

	public function test_c2c_get_current_user_custom_with_before_last_for_single_value() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Mood: happy!', c2c_get_current_user_custom( 'mood', 'Mood: ', '!', 'unknown', ', ', ', and ' ) );
		$this->assertEmpty( c2c_get_current_user_custom( 'nonexistent', 'Mood: ', '!', '', ', ', ', and' ) );
	}

	public function test_c2c_get_current_user_custom_with_before_last_for_two_values() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Colors: blue, and white.', c2c_get_current_user_custom( 'color', 'Colors: ', '.', 'none', ', ', ', and ' ) );
	}

	public function test_c2c_get_current_user_custom_with_before_last_for_multiple_values() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'Children: adam, bob, cerise, and diane.', c2c_get_current_user_custom( 'child', 'Children: ', '.', 'none', ', ', ', and ' ) );
	}

	/* c2c_get_author_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) */

	public function test_c2c_get_author_custom_with_field() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'happy',      c2c_get_author_custom( 'mood' ) );
		$this->assertEquals( 'Denver, CO', c2c_get_author_custom( 'location' ) );
		$this->assertEmpty( c2c_get_author_custom( 'nonexistent' ) );
	}

	public function test_c2c_get_author_custom_with_before() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Mood: happy', c2c_get_author_custom( 'mood', 'Mood: ' ) );
		$this->assertEmpty( c2c_get_author_custom( 'nonexistent', 'Mood: ' ) );
	}

	public function test_c2c_get_author_custom_with_after() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Mood: happy!', c2c_get_author_custom( 'mood', 'Mood: ', '!' ) );
		$this->assertEmpty( c2c_get_author_custom( 'nonexistent', 'Mood: ', '!' ) );
	}

	public function test_c2c_get_author_custom_with_none() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Mood: happy!',   c2c_get_author_custom( 'mood', 'Mood: ', '!', 'unknown' ) );
		$this->assertEquals( 'Mood: unknown!', c2c_get_author_custom( 'nonexistent', 'Mood: ', '!', 'unknown' ) );
	}

	public function test_c2c_get_author_custom_with_between_for_single_value() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Mood: happy!', c2c_get_author_custom( 'mood', 'Mood: ', '!', 'unknown', ', ' ) );
		$this->assertEmpty( c2c_get_author_custom( 'nonexistent', 'Mood: ', '!', '', ', ' ) );
	}

	public function test_c2c_get_author_custom_with_between_for_two_values() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Colors: blue, white.', c2c_get_author_custom( 'color', 'Colors: ', '.', 'none', ', ' ) );
	}

	public function test_c2c_get_author_custom_with_between_for_multiple_values() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Children: adam, bob, cerise, diane.', c2c_get_author_custom( 'child', 'Children: ', '.', 'none', ', ' ) );
	}

	public function test_c2c_get_author_custom_with_before_last_for_single_value() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Mood: happy!', c2c_get_author_custom( 'mood', 'Mood: ', '!', 'unknown', ', ', ', and ' ) );
		$this->assertEmpty( c2c_get_author_custom( 'nonexistent', 'Mood: ', '!', '', ', ', ', and' ) );
	}

	public function test_c2c_get_author_custom_with_before_last_for_two_values() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Colors: blue, and white.', c2c_get_author_custom( 'color', 'Colors: ', '.', 'none', ', ', ', and ' ) );
	}

	public function test_c2c_get_author_custom_with_before_last_for_multiple_values() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'Children: adam, bob, cerise, and diane.', c2c_get_author_custom( 'child', 'Children: ', '.', 'none', ', ', ', and ' ) );
	}

	/* c2c_get_user_custom( $user_id, $field, $before='', $after='', $none='', $between='', $before_last='' ) */

	public function test_c2c_get_user_custom_with_field() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'happy',      c2c_get_user_custom( $user_id, 'mood' ) );
		$this->assertEquals( 'Denver, CO', c2c_get_user_custom( $user_id, 'location' ) );
		$this->assertEmpty( c2c_get_user_custom( $user_id, 'nonexistent' ) );
	}

	public function test_c2c_get_user_custom_with_before() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Mood: happy', c2c_get_user_custom( $user_id, 'mood', 'Mood: ' ) );
		$this->assertEmpty( c2c_get_user_custom( $user_id, 'nonexistent', 'Mood: ' ) );
	}

	public function test_c2c_get_user_custom_with_after() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Mood: happy!', c2c_get_user_custom( $user_id, 'mood', 'Mood: ', '!' ) );
		$this->assertEmpty( c2c_get_user_custom( $user_id, 'nonexistent', 'Mood: ', '!' ) );
	}

	public function test_c2c_get_user_custom_with_none() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Mood: happy!',   c2c_get_user_custom( $user_id, 'mood', 'Mood: ', '!', 'unknown' ) );
		$this->assertEquals( 'Mood: unknown!', c2c_get_user_custom( $user_id, 'nonexistent', 'Mood: ', '!', 'unknown' ) );
	}

	public function test_c2c_get_user_custom_with_between_for_single_value() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Mood: happy!', c2c_get_user_custom( $user_id, 'mood', 'Mood: ', '!', 'unknown', ', ' ) );
		$this->assertEmpty( c2c_get_user_custom( $user_id, 'nonexistent', 'Mood: ', '!', '', ', ' ) );
	}

	public function test_c2c_get_user_custom_with_between_for_two_values() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Colors: blue, white.', c2c_get_user_custom( $user_id, 'color', 'Colors: ', '.', 'none', ', ' ) );
	}

	public function test_c2c_get_user_custom_with_between_for_multiple_values() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Children: adam, bob, cerise, diane.', c2c_get_user_custom( $user_id, 'child', 'Children: ', '.', 'none', ', ' ) );
	}

	public function test_c2c_get_user_custom_with_before_last_for_single_value() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Mood: happy!', c2c_get_user_custom( $user_id, 'mood', 'Mood: ', '!', 'unknown', ', ', ', and ' ) );
		$this->assertEmpty( c2c_get_user_custom( $user_id, 'nonexistent', 'Mood: ', '!', '', ', ', ', and' ) );
	}

	public function test_c2c_get_user_custom_with_before_last_for_two_values() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Colors: blue, and white.', c2c_get_user_custom( $user_id, 'color', 'Colors: ', '.', 'none', ', ', ', and ' ) );
	}

	public function test_c2c_get_user_custom_with_before_last_for_multiple_values() {
		$user_id = $this->create_user_with_meta();

		$this->assertEquals( 'Children: adam, bob, cerise, and diane.', c2c_get_user_custom( $user_id, 'child', 'Children: ', '.', 'none', ', ', ', and ' ) );
	}

	/*
	 * Shortcode.
	 *
	 * [user_custom_field field="" user_id="" this_post="" before="" after="" none="" between="" before_last="" id="" class=""]
	 */

	public function test_shortcode_class_exists() {
		$this->assertTrue( class_exists( 'c2c_GetUserCustomFieldValuesShortcode' ) );
	}

	public function test_shortcode_version() {
		$this->assertEquals( '004', c2c_GetUserCustomFieldValuesShortcode::version() );
	}

	public function test_shortcode_hooks_init() {
		$this->assertEquals( 11, has_filter( 'init', array( 'c2c_GetUserCustomFieldValuesShortcode', 'register' ) ) );
	}

	public function test_shortcode_with_field() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( 'happy', do_shortcode( '[user_custom_field field="mood"]' ) );
	}

	public function test_shortcode_with_field_and_id_and_class() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( '<span id="the-id" class="the-class">happy</span>', do_shortcode( '[user_custom_field field="mood" id="the-id" class="the-class"]' ) );
	}

	public function test_shortcode_with_this_post() {
		$user_id = $this->create_user_with_meta();
		$post_id = $this->factory->post->create( array( 'post_author' => $user_id ) );

		query_posts( '' );
		the_post();

		$this->assertEquals( 'happy', do_shortcode( '[user_custom_field field="mood" this_post="1"]' ) );
		$this->assertEquals( 'happy', do_shortcode( '[user_custom_field field="mood" user_id="0"]' ) );
	}

	public function test_shortcode_with_post_id() {
		$user_id1 = $this->create_user_with_meta();
		$user_id2 = $this->create_user_with_meta( array( 'mood' => 'tired' ) );

		$this->assertEquals( 'happy', do_shortcode( '[user_custom_field field="mood" user_id="' . $user_id1 . '"]' ) );
		$this->assertEquals( 'tired', do_shortcode( '[user_custom_field field="mood" user_id="' . $user_id2 . '"]' ) );
	}

	public function test_shortcode_with_before_and_after_and_between_and_before_last() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals(
			'Kids: adam, bob, cerise, and diane!',
			do_shortcode( '[user_custom_field field="child" user_id="" before="Kids: " after="!" between=", " before_last=", and "]' )
		);
	}

	/* Widget */

	public function test_widget_class_exists() {
		$this->assertTrue( class_exists( 'c2c_GetUserCustomWidget' ) );
	}

	public function test_widget_version() {
		$this->assertEquals( '012', c2c_GetUserCustomWidget::version() );
	}

	public function test_widget_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_Widget_013' ) );
	}

	public function test_widget_framework_version() {
		$this->assertEquals( '013', c2c_Widget_013::version() );
	}

	public function test_widget_hooks_widgets_init() {
		$this->assertEquals( 10, has_filter( 'widgets_init', array( 'c2c_GetUserCustomWidget', 'register_widget' ) ) );
	}

	public function test_widget_made_available() {
		$this->assertContains( 'c2c_GetUserCustomWidget', array_keys( $GLOBALS['wp_widget_factory']->widgets ) );
	}

}
