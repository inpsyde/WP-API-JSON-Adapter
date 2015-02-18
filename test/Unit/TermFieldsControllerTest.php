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
		$data = array(
			array(
				'ID' => 1,
				'name' => 'Apple',
				'slug' => 'apple'
			),
			array(
				'ID' => 2,
				'name' => 'Banana',
				'slug' => 'banana'
			),
		);

		$expected_data = array();
		foreach ( $data as $term ) {
			$term[ 'taxonomy' ] = 'category';
			$expected_data[] = (object) $term;
		}

		$json_response_mock->expects( $this->atLeast( 1 ) )
			->method( 'get_data' )
			->willReturn( $data );

		$json_response_mock->expects( $this->atLeast( 1 ) )
			->method( 'set_data' )
			->with(
				$this->callback(
					function( $data ){
						foreach ( $data as $term ) {
							if ( ! is_object( $term ) )
								return FALSE;
							if ( ! isset( $term->taxonomy ) )
								return FALSE;
							if ( 'category' !== $term->taxonomy )
								return FALSE;
						}

						return TRUE;
					}
				)
			);

		$entity_controller_mock = $this->get_entity_fields_controller_mock();
		$entity_controller_mock->expects( $this->exactly( 1 ) )
			->method( 'dispatch' )
			->with( $json_response_mock );
		$entity_controller_mock->expects( $this->exactly( 2  ) )
			->method( 'entity_to_object' )
			->willReturnCallback(
				function( array $data ) {
					return (object) $data;
				}
			);

		$edit_repo_mock = $this->get_field_repository_mock();
		$add_repo_mock = $this->get_field_repository_mock();

		$json_server_mock = new \WP_JSON_Server;
		$json_server_mock->path = '/taxonomy/category/terms';

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

		\WP_Mock::wpFunction(
			'taxonomy_exists',
			array(
				'times'  => 1,
				'args'   => 'category',
				'return' => TRUE
			)
		);

		$testee = new Core\TermFieldsController(
			$entity_controller_mock,
			$edit_repo_mock,
			$add_repo_mock
		);
		$testee->set_json_server( $json_server_mock );
		$testee->dispatch( $json_response_mock );
	}

	public function test_get_taxonomy() {

		$json_server_mock = new \WP_JSON_Server;
		$json_server_mock->path = '/taxonomy/category/terms';

		$entity_controller_mock = $this->get_entity_fields_controller_mock();

		$edit_repo_mock = $this->get_field_repository_mock();
		$add_repo_mock = $this->get_field_repository_mock();

		$testee = new Core\TermFieldsController(
			$entity_controller_mock,
			$edit_repo_mock,
			$add_repo_mock
		);

		\WP_Mock::wpFunction(
			'taxonomy_exists',
			array(
				'times'  => 1,
				'args'   => 'category',
				'return' => TRUE
		    )
		);
		$testee->set_json_server( $json_server_mock );
		$this->assertEquals(
			'category',
			$testee->get_taxonomy()
		);
	}
}
 