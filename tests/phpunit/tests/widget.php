<?php

defined( 'ABSPATH' ) or die();

class Get_User_Custom_Field_Values_Widget_Test extends WP_UnitTestCase {

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

	private function widget_init( $config = array() ) {
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		c2c_GetUserCustomWidget::register_widget();
		$widget = new c2c_GetUserCustomWidget( 'abc_abc', '', array() );

		$default_config = array();
		foreach ( $widget->get_config() as $key => $val ) {
			$default_config[ $key ] = $val['default'];
		}
		$config = array_merge( $default_config, $config );

		if ( true === $config['user_id'] ) {
			$config['user_id'] = $user_id;
		}

		$settings = array( 'before_title' => '', 'after_title' => '', );

		return array( $user_id, $widget, $config, $settings );
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

	/* Widget */

	public function test_widget_class_exists() {
		$this->assertTrue( class_exists( 'c2c_GetUserCustomWidget' ) );
	}

	public function test_widget_version() {
		$this->assertEquals( '013', c2c_GetUserCustomWidget::version() );
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
		$this->assertArrayHasKey( 'c2c_GetUserCustomWidget', $GLOBALS['wp_widget_factory']->widgets );
	}

	public function test_widget_body_with_class() {
		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'mood', 'class' => 'abcd', 'user_id' => true )  );

		$this->assertEquals( '<span class="abcd">happy</span>', $widget->widget_body( $config, '', $settings ) );
	}

	public function test_widget_body_with_id() {
		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'mood', 'id' => 'myid', 'user_id' => true ) );

		$this->assertEquals( '<span id="myid">happy</span>', $widget->widget_body( $config, '', $settings ) );
	}

	public function test_widget_body_with_class_and_id() {
		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'mood', 'class' => 'abcd', 'id' => 'myid', 'user_id' => true ) );

		$this->assertEquals( '<span id="myid" class="abcd">happy</span>', $widget->widget_body( $config, '', $settings ) );
	}

	public function test_widget_body_with_class_and_id_but_no_meta_value() {
		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'nonexistent', 'class' => 'abcd', 'id' => 'myid', 'user_id' => true ) );

		$this->assertEmpty( $widget->widget_body( $config, '', $settings ) );
	}

	public function test_widget_with_user_id_of_current_in_invalid_situation() {
		$author_id = $this->create_user_with_meta( array( 'mood' => 'confused' ) );

		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'mood', 'user_id' => 'current' ) );

		$this->assertNull( $widget->widget_body( $config, '', $settings ) );
	}

	public function test_widget_with_user_id_of_current() {
		$author_id = $this->create_user_with_meta( array( 'mood' => 'perplexed' ) );
		$user_id = $this->create_user_with_meta();
		wp_set_current_user( $user_id );

		// Simulate conditions when it is valid to run.
		$GLOBALS['authordata'] = get_userdata( $author_id );
		$GLOBALS['wp_query']->is_single = true;

		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'mood', 'user_id' => 'current' ) );

		$this->assertEquals( 'perplexed', $widget->widget_body( $config, '', $settings ) );
	}

	public function test_widget_with_explicit_user_id() {
		$u_id = $this->create_user_with_meta( array( 'mood' => 'joyous' ) );
		$user_id = $this->create_user_with_meta();

		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'mood', 'user_id' => $u_id ) );

		$this->assertEquals( 'joyous', $widget->widget_body( $config, '', $settings ) );
	}

	public function test_widget_with_no_user_id() {
		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'mood' ) );

		$u_id = $this->create_user_with_meta( array( 'mood' => 'jazzed' ) );
		wp_set_current_user( $u_id );


		$this->assertEquals( 'jazzed', $widget->widget_body( $config, '', $settings ) );
	}

	public function test_widget_with_no_user_id_and_no_current_user() {
		$user_id = $this->create_user_with_meta( array( 'mood' => 'befuddled' ) );
		list( $user_id, $widget, $config, $settings ) = $this->widget_init( array( 'field' => 'mood' ) );

		$this->unset_current_user();

		$this->assertNull( $widget->widget_body( $config, '', $settings ) );
	}

}
