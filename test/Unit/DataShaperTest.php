<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter\Test\TestCase;
use WPAPIAdapter\Core;

class DataShaperTest extends TestCase\MockCollectionTestCase {

	/**
	 * runs before each test
	 */
	public function setUp() {
		\WP_Mock::setUp();
	}

	/**
	 * runs after eacht test
	 */
	public function tearDown() {
		\WP_Mock::tearDown();
	}

	/**
	 * @see Core\DataShaper::shape_data()
	 */
	public function test_shape_data() {

		// Mock of WP_JSON_Response
		$response_mock = $this->get_json_response_mock();

		// Mock of WP_JSON_Server
		$server_mock = $this->get_json_server_mock();
		$server_mock->expects( $this->exactly( 1 ) )
			->method( 'dispatch' )
			->willReturn( $response_mock );

		// Mock of Route\EndpointParser
		$parser_mock   = $this->get_endpoint_parser_mock();
		$parser_mock->expects( $this->exactly( 1 ) )
			->method( 'get_entity' )
			->willReturn( 'posts' );

		// Mock of Builder\EndpointParserBuilder
		$builder_mock  = $this->get_endpoint_parser_builder_mock();
		$builder_mock->expects( $this->exactly( 1 ) )
			->method( 'build_endpoint_parser' )
			->with( $server_mock )
			->willReturn( $parser_mock );

		// Mock of a FieldsController
		$fields_controller_mock = $this->get_post_fields_controller_mock();
		$fields_controller_mock->expects( $this->exactly( 1 ) )
			->method( 'dispatch' )
			->with( $response_mock );

		// Mock of is_wp_error()
		\WP_Mock::wpFunction(
			'is_wp_error',
			array(
				'times'  => 1,
				'args'   => array( $response_mock ),
				'return' => FALSE
			)
		);

		$testee = new Core\DataShaper( $builder_mock );
		$testee->add_entity_controller( 'posts', $fields_controller_mock );
		$result = $testee->shape_data( NULL, $server_mock );

		$this->assertSame(
			$response_mock,
			$result
		);
	}

	/**
	 * @see Core\DataShaper::shape_data()
	 */
	public function test_shape_data_with_error_as_parameter() {

		// Mock of WP_JSON_Response
		$response_mock = $this->get_json_response_mock();

		// Mock of WP_JSON_Server
		$server_mock = $this->get_json_server_mock();
		$server_mock->expects( $this->never() )
			->method( 'dispatch' );

		// Mock of Route\EndpointParser
		$parser_mock   = $this->get_endpoint_parser_mock();
		$parser_mock->expects( $this->exactly( 1 ) )
			->method( 'get_entity' )
			->willReturn( 'posts' );

		// Mock of Builder\EndpointParserBuilder
		$builder_mock  = $this->get_endpoint_parser_builder_mock();
		$builder_mock->expects( $this->exactly( 1 ) )
			->method( 'build_endpoint_parser' )
			->with( $server_mock )
			->willReturn( $parser_mock );

		// Mock of a FieldsController
		$fields_controller_mock = $this->get_post_fields_controller_mock();
		$fields_controller_mock->expects( $this->never() )
			->method( 'dispatch' );

		$wp_error_mock = $this->get_wp_error_mock();
		// Mock of is_wp_error()
		\WP_Mock::wpFunction(
			'is_wp_error',
			array(
				'times'  => 1,
				'args'   => array( $wp_error_mock ),
				'return' => TRUE
			)
		);

		$testee = new Core\DataShaper( $builder_mock );
		$testee->add_entity_controller( 'posts', $fields_controller_mock );
		$result = $testee->shape_data( $wp_error_mock, $server_mock );

		$this->assertSame(
			$wp_error_mock,
			$result
		);
	}

	/**
	 * @see Core\DataShaper::shape_data()
	 */
	public function test_shape_data_with_error_from_server() {

		// Mock of WP_Error
		$wp_error_mock = $this->get_wp_error_mock();

		// Mock of WP_JSON_Response
		$response_mock = $this->get_json_response_mock();

		// Mock of WP_JSON_Server
		$server_mock = $this->get_json_server_mock();
		$server_mock->expects( $this->exactly( 1 ) )
			->method( 'dispatch' )
			->willReturn( $wp_error_mock );

		// Mock of Route\EndpointParser
		$parser_mock   = $this->get_endpoint_parser_mock();
		$parser_mock->expects( $this->exactly( 1 ) )
			->method( 'get_entity' )
			->willReturn( 'posts' );

		// Mock of Builder\EndpointParserBuilder
		$builder_mock  = $this->get_endpoint_parser_builder_mock();
		$builder_mock->expects( $this->exactly( 1 ) )
			->method( 'build_endpoint_parser' )
			->with( $server_mock )
			->willReturn( $parser_mock );

		// Mock of a FieldsController
		$fields_controller_mock = $this->get_post_fields_controller_mock();
		$fields_controller_mock->expects( $this->never() )
			->method( 'dispatch' );

		// Mock of is_wp_error()
		\WP_Mock::wpFunction(
			'is_wp_error',
			array(
				'times'  => 1,
				'args'   => array( $wp_error_mock ),
				'return' => TRUE
			)
		);

		$testee = new Core\DataShaper( $builder_mock );
		$testee->add_entity_controller( 'posts', $fields_controller_mock );
		$result = $testee->shape_data( NULL, $server_mock );

		$this->assertSame(
			$wp_error_mock,
			$result
		);
	}

	/**
	 * @see Core\DataShaper::shape_data()
	 */
	public function test_shape_data_with_sanitizing() {

		$array_data = array();
		// Mock of WP_JSON_Server
		$server_mock = $this->get_json_server_mock();
		$server_mock->expects( $this->exactly( 1 ) )
			->method( 'dispatch' )
			->willReturn( $array_data );

		// Mock of Route\EndpointParser
		$parser_mock   = $this->get_endpoint_parser_mock();
		$parser_mock->expects( $this->exactly( 1 ) )
			->method( 'get_entity' )
			->willReturn( 'posts' );

		// Mock of Builder\EndpointParserBuilder
		$builder_mock  = $this->get_endpoint_parser_builder_mock();
		$builder_mock->expects( $this->exactly( 1 ) )
			->method( 'build_endpoint_parser' )
			->with( $server_mock )
			->willReturn( $parser_mock );

		// Mock of a FieldsController
		$fields_controller_mock = $this->get_post_fields_controller_mock();
		$fields_controller_mock->expects( $this->exactly( 1 ) )
			->method( 'dispatch' )
			->with( $this->isInstanceOf( '\WP_JSON_Response' ) );

		// Mock of is_wp_error()
		\WP_Mock::wpFunction(
			'is_wp_error',
			array(
				'times'  => 1,
				'args'   => array( $array_data ),
				'return' => FALSE
			)
		);

		$testee = new Core\DataShaper( $builder_mock );
		$testee->add_entity_controller( 'posts', $fields_controller_mock );
		$result = $testee->shape_data( NULL, $server_mock );

		$this->assertInstanceOf(
			'\WP_JSON_Response',
			$result
		);
	}

	/**
	 * @see Core\DataShaper::sanitize_response()
	 */
	public function test_sanitize_response() {

		$builder_mock = $this->get_endpoint_parser_builder_mock();
		$testee = new Core\DataShaper( $builder_mock );

		$this->assertInstanceOf(
			'\WP_JSON_ResponseInterface',
			$testee->sanitize_response( new \WP_JSON_Response() )
		);

		$this->assertInstanceOf(
			'\WP_JSON_ResponseInterface',
			$testee->sanitize_response( array() )
		);

		$this->assertInstanceOf(
			'\WP_JSON_ResponseInterface',
			$testee->sanitize_response( 42 )
		);

		$this->assertInstanceOf(
			'\WP_JSON_ResponseInterface',
			$testee->sanitize_response( NULL )
		);
	}
}
 