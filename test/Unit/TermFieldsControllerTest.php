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
	 * @dataProvider dispatch_provider
	 * @param mixed $response_data
	 * @param bool $single_entity
	 * @param string $taxonomy
	 * @param mixed $expected
	 */
	public function test_dispatch( $response_data, $single_entity, $taxonomy, $expected ) {

		$json_response_mock = $this->get_json_response_mock();
		$json_response_mock->expects( $this->atLeast( 1 ) )
			->method( 'get_data' )
			->willReturn( $response_data );

		$phpunit = $this;
		$json_response_mock->expects( $this->atLeast( 1 ) )
			->method( 'set_data' )
			->with(
				$this->callback(
					function( $data ) use ( $expected, $phpunit ) {
						// it's better to use an assertion here because it displays the
						// diffs when it fails
						$phpunit->assertEquals( $expected, $data );
						return TRUE;
					}
				)
			);

		$entity_controller_mock = $this->get_entity_fields_controller_mock();
		$entity_controller_mock->expects( $this->exactly( 1 ) )
			->method( 'dispatch' )
			->with( $json_response_mock );
		$entity_controller_mock->expects( $this->atLeast( 1 ) )
			->method( 'entity_to_object' )
			->willReturnCallback(
				function( array $data ) {
					return (object) $data;
				}
			);

		$edit_repo_mock = $this->get_field_repository_mock();
		$add_repo_mock = $this->get_field_repository_mock();

		$json_server_mock = new \WP_JSON_Server;
		$json_server_mock->path = '/taxonomy/' . $taxonomy .'/terms';

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
				'args'   => $taxonomy,
				'return' => TRUE
			)
		);

		$testee = new Core\TermFieldsController(
			$entity_controller_mock,
			$edit_repo_mock,
			$add_repo_mock
		);
		$testee->set_single_entity( $single_entity );
		$testee->set_json_server( $json_server_mock );
		$testee->dispatch( $json_response_mock );
	}

	/**
	 * @see TermFieldsController::get_taxonomy()
	 */
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

	/**
	 * @see test_dispatch()
	 * @return array
	 */
	public function dispatch_provider() {

		$data = array(
			#0:
			array(
				#1. Parameter $response_data
				array(
					array(
						'ID'   => 1,
						'name' => 'Apple',
						'slug' => 'apple'
					),
					array(
						'ID'   => 2,
						'name' => 'Banana',
						'slug' => 'banana'
					),
				),

				#2. parameter $single_entity
				FALSE,
				#3. parameter $taxonomy
				'category',
				#4. parameter $expected output
				array(
					(object) array(
						'ID'       => 1,
						'name'     => 'Apple',
						'slug'     => 'apple',
						'taxonomy' => 'category'
					),
					(object) array(
						'ID'       => 2,
						'name'     => 'Banana',
						'slug'     => 'banana',
						'taxonomy' => 'category'
					),
				)
			),
			#1:
			array(
				#1. Parameter $response_data
				array(
					'ID'   => 1,
					'name' => 'Apple',
					'slug' => 'apple'
				),

				#2. parameter $single_entity
				TRUE,
				#3. parameter $taxonomy
				'category',
				#4. parameter $expected output
				(object) array(
					'ID'       => 1,
					'name'     => 'Apple',
					'slug'     => 'apple',
					'taxonomy' => 'category'
				)
			)
		);

		return $data;
	}
}
 