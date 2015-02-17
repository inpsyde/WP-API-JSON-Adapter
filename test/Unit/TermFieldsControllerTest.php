<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter\Test\TestCase;
use WPAPIAdapter\Core;

class TermFieldsControllerTest extends TestCase\MockCollectionTestCase {

	/**
	 * runs before each test
	 */
	public function setUp() {

		\WP_Mock::setUp();
	}

	/**
	 * runs before each test
	 */
	public function tearDown() {

		\WP_Mock::tearDown();
	}

	/**
	 * Test if the desired filters gets properly called.
	 *
	 * @see PostFieldsController::dispatch()
	 */
	public function test_dispatch() {

		$json_response_mock = $this->get_json_response_mock();

		$entity_controller_mock = $this->get_entity_fields_controller_mock();
		$entity_controller_mock->expects( $this->exactly( 1 ) )
			->method( 'dispatch' )
			->with( $json_response_mock );

		$edit_repo_mock = $this->get_field_repository_mock();
		$add_repo_mock = $this->get_field_repository_mock();

		/**
		 * wp mock
		 * there seems to be a bug…
		 *
		 * PHP Fatal error:  Call to a member function react() on a non-object in vendor/10up/wp_mock/WP_Mock/Action.php on line 37
		 *
		 * Tested with phpunit 4.3.* and 4.4.5
		 *
		 * It seems that the error occurs when the number of arguments passed to the mock
		 * does not match number of arguments passed to the hook itself, so be careful
		 *
		 * Todo: extract the problem and write an issue
		 */
		\WP_Mock::expectAction( 'wpapiadapter_register_term_change_field_handler', $edit_repo_mock );
		\WP_Mock::expectAction( 'wpapiadapter_register_term_add_field_handler', $add_repo_mock );

		$testee = new Core\TermFieldsController(
			$entity_controller_mock,
			$edit_repo_mock,
			$add_repo_mock
		);
		$testee->dispatch( $json_response_mock );
	}
}
 