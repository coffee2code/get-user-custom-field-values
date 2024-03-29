<?php

defined( 'ABSPATH' ) or die();

class Get_User_Custom_Field_Values_Shortcode_Test extends WP_UnitTestCase {

	public function tearDown() {
		parent::tearDown();
		$this->unset_current_user();
		$GLOBALS['post'] = null;
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

	private function create_post( $post_data = array(), $make_global = false ) {
		$post_id = $this->factory->post->create( $post_data );

		if ( $make_global ) {
			global $post;
			$post = get_post( $post_id );
		}

		return $post_id;
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
			'secret'      =>  'abc',
		);
	}


	//
	//
	// TESTS
	//
	//

	/*
	 * Shortcode.
	 *
	 * [user_custom_field field="" user_id="" this_post="" before="" after="" none="" between="" before_last="" id="" class=""]
	 */

	public function test_shortcode_class_exists() {
		$this->assertTrue( class_exists( 'c2c_GetUserCustomFieldValuesShortcode' ) );
	}

	public function test_shortcode_version() {
		$this->assertEquals( '008', c2c_GetUserCustomFieldValuesShortcode::version() );
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

	public function test_shortcode_with_field_and_id() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( '<span id="the-id">happy</span>', do_shortcode( '[user_custom_field field="mood" id="the-id"]' ) );
	}

	public function test_shortcode_with_field_and_class() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals( '<span class="the-class">happy</span>', do_shortcode( '[user_custom_field field="mood" class="the-class"]' ) );
	}

	public function test_shortcode_with_field_and_no_id() {
		$user_id1 = $this->create_user_with_meta( array( 'mood' => 'pleased' ) );
		$user_id2 = $this->create_user_with_meta();
		wp_set_current_user( $user_id1 );

		$this->assertEquals( 'pleased', do_shortcode( '[user_custom_field field="mood" between=" and "]' ) );
	}

	public function test_shortcode_with_this_post() {
		$user_id = $this->create_user_with_meta( array(), array( 'role' => 'editor' ) );
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

	public function test_shortcode_with_double_quotes_in_attribute() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals(
			'<strong class="url">adam, bob, cerise, diane</strong>',
			do_shortcode( '[user_custom_field field="child" before=\'<strong class="url">\' after="</strong>" between=", " /]' )
		);
	}

	public function test_shortcode_with_single_quotes_in_attribute() {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		$this->assertEquals(
			"<strong class='url'>adam, bob, cerise, diane</strong>",
			do_shortcode( '[user_custom_field field="child" before="<strong class=\'url\'>" after="</strong>" between=", " /]' )
		);
	}

	public function test_shortcode_with_contributor_as_author_does_not_work_in_post_context() {
		$privileged_user_id  = $this->create_user_with_meta( array( 'secret' => 'xyz' ), array( 'role' => 'administrator' ) );
		$contributor_id      = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );
		$contributor_post_id = $this->create_post( array(
			'post_author' => $contributor_id,
			'post_content' => '[user_custom_field field="secret" user_id="' . $privileged_user_id . '"]',
		), true );

		$this->assertEmpty( trim( apply_filters( 'the_content', get_the_content() ) ) );
	}

	public function test_shortcode_with_author_as_author_does_not_work_in_post_context() {
		$privileged_user_id  = $this->create_user_with_meta( array( 'secret' => 'xyz' ), array( 'role' => 'administrator' ) );
		$author_id           = $this->create_user_with_meta( false, array( 'role' => 'author' ) );
		$author_post_id      = $this->create_post( array(
			'post_author' => $author_id,
			'post_content' => '[user_custom_field field="secret" user_id="' . $privileged_user_id . '"]',
		), true );

		$this->assertEmpty( trim( apply_filters( 'the_content', get_the_content() ) ) );
	}

	public function test_shortcode_with_contributor_as_author_does_not_work_in_post_context_for_administrator() {
		$privileged_user_id  = $this->create_user_with_meta( array( 'secret' => 'xyz' ), array( 'role' => 'administrator' ) );
		$contributor_id      = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );
		$contributor_post_id = $this->create_post( array(
			'post_author'  => $contributor_id,
			'post_content' => '[user_custom_field field="secret" user_id="' . $privileged_user_id . '"]',
		), true );
		$administrator_id    = $this->create_user_with_meta( false, array( 'role' => 'administrator' ) );
		wp_set_current_user( $administrator_id );

		$this->assertEmpty( trim( apply_filters( 'the_content', get_the_content() ) ) );
	}

	public function test_shortcode_outside_post_context_of_post_with_contributor_as_author_works() {
		$privileged_user_id  = $this->create_user_with_meta( array( 'secret' => 'xyz' ), array( 'role' => 'administrator' ) );
		$contributor_id      = $this->create_user_with_meta( array( 'secret' => 'xyz' ), array( 'role' => 'contributor' ) );
		$contributor_post_id = $this->create_post( array(
			'post_author' => $contributor_id,
			'post_content' => '[user_custom_field field="secret" user_id="' . $privileged_user_id . '"]',
		), true );

		$this->assertEquals( '', do_shortcode( '[user_custom_field field="secret" user_id="' . $privileged_user_id . '"]' ) );
	}

	public function test_shortcode_with_editor_as_author_works() {
		$privileged_user_id  = $this->create_user_with_meta( array( 'secret' => 'xyz' ), array( 'role' => 'administrator' ) );
		$editor_id          = $this->create_user_with_meta( false, array( 'role' => 'editor' ) );
		$editor_post_id     = $this->create_post( array(
			'post_author' => $editor_id,
			'post_content' => '[user_custom_field field="secret" user_id="' . $privileged_user_id . '"]',
		), true );

		$this->assertEquals( 'xyz', trim( apply_filters( 'the_content', get_the_content() ) ) );
	}

	/*
	 * can_author_use_shortcodes()
	 */

	public function test_can_author_use_shortcodes_no_args_uses_current_post() {
		$contributor_id = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );
		$post1_id       = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes() );

		$editor_id = $this->create_user_with_meta( false, array( 'role' => 'editor' ) );
		$post2_id  = $this->create_post( array( 'post_author' => $editor_id ), true );

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes() );
	}

	public function test_can_author_use_shortcodes_with_user() {
		$contributor_id = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( $contributor_id ) );
		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( get_userdata( $contributor_id ) ) );

		$editor_id = $this->create_user_with_meta( false, array( 'role' => 'editor' ) );

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( $editor_id ) );
		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( get_userdata( $editor_id ) ) );
	}

	public function test_can_author_use_shortcodes_with_post() {
		$contributor_id = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );
		$post1_id = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( null, $post1_id ) );
		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( null, get_post( $post1_id ) ) );

		$editor_id = $this->create_user_with_meta( false, array( 'role' => 'editor' ) );
		$post2_id  = $this->create_post( array( 'post_author' => $editor_id ), true );

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( null, $post2_id ) );
		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( null, get_post( $post2_id ) ) );
	}

	public function test_can_author_use_shortcodes_uses_user_if_it_and_post_are_provided() {
		$contributor_id = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );
		$editor_id      = $this->create_user_with_meta( false, array( 'role' => 'editor' ) );

		$post1_id = $this->create_post( array( 'post_author' => $editor_id ), true );

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( $contributor_id, $post1_id ) );
		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( get_userdata( $contributor_id ), $post1_id ) );

		$post2_id = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( $editor_id, $post2_id ) );
		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( get_userdata( $editor_id ), $post2_id ) );
	}

	/*
	 * filter: get_user_custom_field_values/can_author_use_shortcodes
	 */

	public function test_filter_can_author_use_shortcodes() {
		add_filter( 'get_user_custom_field_values/can_author_use_shortcodes', function( $can, $user, $post ) {
			return true;
		}, 10, 3 );

		$contributor_id = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );
		$post1_id       = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes() );
	}

	public function test_filter_can_author_use_shortcodes_takes_into_account_user() {
		add_filter( 'get_user_custom_field_values/can_author_use_shortcodes', function( $can, $user, $post ) {
			if ( $user && 'testadmin' === $user->user_nicename ) {
				$can = false;
			}
			return $can;
		}, 10, 3 );

		$admin1_id = $this->create_user_with_meta( false, array( 'role' => 'administrator', 'user_nicename' => 'testadmin' ) );
		$admin2_id = $this->create_user_with_meta( false, array( 'role' => 'administrator', 'user_nicename' => 'anotheradmin' ) );

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( $admin1_id ) );
		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes( $admin2_id ) );
	}

	public function test_filter_can_author_use_shortcodes_gets_cast_to_bool() {
		add_filter( 'get_user_custom_field_values/can_author_use_shortcodes', function( $can, $user, $post ) {
			return 5;
		}, 10, 3 );

		$contributor_id = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );
		$post1_id       = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertTrue( true === c2c_GetUserCustomFieldValuesShortcode::$instance->can_author_use_shortcodes() );
	}

	/*
	 * show_metabox()
	 */

	public function test_show_metabox_when_not_in_block_editor() {
		set_current_screen( 'post.php' );
		$current_screen = get_current_screen();
		$current_screen->is_block_editor = false;

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );
	}

	public function test_show_metabox_when_in_block_editor() {
		set_current_screen( 'post.php' );
		$current_screen = get_current_screen();
		$current_screen->is_block_editor = true;

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );
	}

	public function test_show_metabox_when_author_cannot_publish_posts() {
		$this->test_show_metabox_when_not_in_block_editor();

		$contributor_id = $this->create_user_with_meta( false, array( 'role' => 'contributor' ) );
		$post1_id       = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );
	}

	public function test_show_metabox_when_author_can_publish_posts() {
		$this->test_show_metabox_when_not_in_block_editor();

		$editor_id = $this->create_user_with_meta( false, array( 'role' => 'editor' ) );
		$post_id   = $this->create_post( array( 'post_author' => $editor_id ), true );

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );
	}

	/*
	 * filter: get_user_custom_field_values/show_metabox
	 */

	public function test_filter_show_metabox() {
		set_current_screen( 'post.php' );
		$current_screen = get_current_screen();
		$current_screen->is_block_editor = false;
		add_filter( 'get_user_custom_field_values/show_metabox', function( $show ) {
			return true;
		} );

		$contributor_id = $this->create_user_with_meta( array(), array( 'role' => 'contributor' ) );
		$post1_id       = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );
	}

	public function test_filter_show_metabox_does_not_fire_if_block_editor_enabled() {
		set_current_screen( 'post.php' );
		$current_screen = get_current_screen();
		$current_screen->is_block_editor = true;
		add_filter( 'get_user_custom_field_values/show_metabox', function( $show ) {
			return true;
		} );

		$contributor_id = $this->create_user_with_meta( array(), array( 'role' => 'contributor' ) );
		$post1_id       = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );
	}

	public function test_filter_show_metabox_takes_into_account_user() {
		set_current_screen( 'post.php' );
		$current_screen = get_current_screen();
		$current_screen->is_block_editor = false;
		add_filter( 'get_user_custom_field_values/show_metabox', function( $show ) {
			global $post;
			$user = get_userdata( $post->post_author );
			if ( $user && 'testadmin' === $user->user_nicename ) {
				$show = false;
			}
			return $show;
		} );

		$admin1_id = $this->create_user_with_meta( array(), array( 'role' => 'administrator', 'user_nicename' => 'testadmin' ) );
		$post1_id  = $this->create_post( array( 'post_author' => $admin1_id ), true );

		$this->assertFalse( c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );

		$admin2_id = $this->create_user_with_meta( array(), array( 'role' => 'administrator', 'user_nicename' => 'anotheradmin' ) );
		$post2_id  = $this->create_post( array( 'post_author' => $admin2_id ), true );

		$this->assertTrue( c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );
	}

	public function test_filter_show_metabox_gets_cast_to_bool() {
		set_current_screen( 'post.php' );
		$current_screen = get_current_screen();
		$current_screen->is_block_editor = false;
		add_filter( 'get_user_custom_field_values/show_metabox', function( $show ) {
			return 5;
		} );

		$contributor_id = $this->create_user_with_meta( array(), array( 'role' => 'contributor' ) );
		$post1_id       = $this->create_post( array( 'post_author' => $contributor_id ), true );

		$this->assertTrue( true === c2c_GetUserCustomFieldValuesShortcode::$instance->show_metabox() );
	}

}
