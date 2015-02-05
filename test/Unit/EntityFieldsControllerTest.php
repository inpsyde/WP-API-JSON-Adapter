<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter;
use WPAPIAdapter\Core;
use WPAPIAdapter\Test\TestCase;
use WP_Mock;

class EntityFieldsControllerTest extends TestCase\MockCollectionTestCase {

	/**
	 * runs before each test
	 */
	public function setUp() {

		WP_Mock::setUp();
	}

	/**
	 * runs after each test
	 */
	public function tearDown() {

		WP_Mock::tearDown();
	}


	/**
	 * @dataProvider test_dispatch_data_provider
	 * @see WPAPIAdapter\Core\PostFieldController::dispatch()
	 * @param array $data
	 * @param array $expected_data
	 */
	public function test_dispatch( array $data, array $expected_data ) {

		$json_response_mock = $this->get_json_response_mock();
		$json_response_mock->expects( $this->atLeast( 1 ) )
			->method( 'get_data' )
			->willReturn( $data );
		$json_response_mock->expects( $this->exactly( 1 ) )
			->method( 'set_data' )
			->with( $expected_data );

		$json_server_mock = $this->get_json_server_mock();

		// mock of a field handler to restructure the author field
		$author_handler = $this->get_rename_field_handler_mock( NULL, NULL, NULL, TRUE );// expects JSON_Server
		$author_handler->expects( $this->atLeast( 1 ) )
			->method( 'get_name' )
			->willReturn( 'author_ID' );
		$author_handler->expects( $this->atLeast( 1 ) )
			->method( 'get_value' )
			->willReturn( 1 ); //must be 1 due to the $expected_data

		/**
		 * Method 'handle' won't invoked for new field handlers!
		 */
		$new_field_handler = $this->getMockBuilder( '\WPAPIAdapter\Field\RenameFieldHandler' )
			->disableOriginalConstructor()
			->getMock();
		$new_field_handler->expects( $this->never() )
			->method( 'handle' );
		$new_field_handler->expects( $this->atLeast( 1 ) )
			->method( 'get_name' )
			->willReturn( 'custom_field' );
		$new_field_handler->expects( $this->atLeast( 1 ) )
			->method( 'get_value' )
			->willReturn( "I'm new" ); //must be 1 due to the $expected_data

		$change_field_handlers = array(
			'author' => array(
				$author_handler
			)
		);

		$add_field_handlers = array(
			'custom_field' => array(
				$new_field_handler
			)
		);

		$edit_field_repo = $this->get_field_repository_mock( $change_field_handlers );
		$add_field_repo = $this->get_field_repository_mock( $add_field_handlers );


		$testee = new Core\EntityFieldsController( $edit_field_repo, $add_field_repo );
		$testee->set_json_server( $json_server_mock );

		$testee->dispatch( $json_response_mock );
	}

	/**
	 * @return \WP_JSON_Server (Mock)
	 */
	public function get_json_response_mock() {

		$mock = $this->getMockBuilder( '\WP_JSON_Response' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}

	/**
	 * @see PostFieldControllerTest::test_dispatch()
	 * @return array
	 */
	public function test_dispatch_data_provider() {

		return array(
			# 0
			array(
				# 1.parameter: original data
				array(
					array(
						'ID' => 1,
						'title' => 'Post Title',
						'status' => 'publish',
						'author' => array(
							'ID' => 1,
							'username' => 'john'
						),
						'content' => '',
						'parent' => 0,
						'link' => 'http://wpapi.dev/hallo-welt/',
						'date' => '2015-01-18T15:48:08'
					),
					array(
						'ID' => 2,
						'title' => 'Post Title 2',
						'status' => 'publish',
						'author' => array(
							'ID' => 1,
							'username' => 'john'
						),
						'content' => '',
						'parent' => 0,
						'link' => 'http://wpapi.dev/hallo-welt/',
						'date' => '2015-01-18T15:48:08'
					),
					array(
						'ID' => 3,
						'title' => 'Post Title 2',
						'status' => 'publish',
						'author' => array(
							'ID' => 1,
							'username' => 'john'
						),
						'content' => '',
						'parent' => 0,
						'link' => 'http://wpapi.dev/hallo-welt/',
						'date' => '2015-01-18T15:48:08'
					)
				),

				# 2. parameter: expected structure
				array(
					(object) array(
						'ID' => 1,
						'title' => 'Post Title',
						'status' => 'publish',
						'content' => '',
						'parent' => 0,
						'link' => 'http://wpapi.dev/hallo-welt/',
						'date' => '2015-01-18T15:48:08',
						'author_ID' => 1,
						'custom_field' => "I'm new"
					),
					(object) array(
						'ID' => 2,
						'title' => 'Post Title 2',
						'status' => 'publish',
						'content' => '',
						'parent' => 0,
						'link' => 'http://wpapi.dev/hallo-welt/',
						'date' => '2015-01-18T15:48:08',
						'author_ID' => 1,
						'custom_field' => "I'm new"
					),
					(object) array(
						'ID' => 3,
						'title' => 'Post Title 2',
						'status' => 'publish',
						'content' => '',
						'parent' => 0,
						'link' => 'http://wpapi.dev/hallo-welt/',
						'date' => '2015-01-18T15:48:08',
						'author_ID' => 1,
						'custom_field' => "I'm new"
					)
				)
			)
		);
	}
}
 