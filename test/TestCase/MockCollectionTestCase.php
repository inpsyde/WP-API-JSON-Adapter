<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\TestCase;
use WPAPIAdapter;

class MockCollectionTestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * @param \stdClass $entity
	 * @param string $name
	 * @param mixed $value
	 * @param bool $expects_server
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of WPAPIAdapter\Field\RenameFieldHandler)
	 */
	public function get_rename_field_handler_mock( \stdClass $entity = NULL, $name = NULL, $value = NULL, $expects_server = FALSE) {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\Field\RenameFieldHandler' )
			->disableOriginalConstructor()
			->getMock();
		$mock->expects( $this->atLeast( 1 ) )
			->method( 'handle' )
			->willReturn( NULL );
		$mock->expects( $this->atLeast( 1 ) )
			->method( 'set_original_entity' )
			->with( $this->isInstanceOf( '\stdClass') );

		if ( $expects_server ) {
			$mock->expects( $this->atLeast( 1 ) )
				->method( 'set_server' )
				->with( $this->isInstanceOf( '\WP_JSON_Server' ) );
		}

		if ( $name ) {
			$mock->expects( $this->atLeast( 1 ) )
				->method( 'get_name' )
				->willReturn( $name );
		}
		if ( $value ) {
			$mock->expects( $this->atLeast( 1 ) )
				->method( 'get_value' )
				->willReturn( $value );
		}

		return $mock;
	}

	/**
	 * @param array $field_handlers (str $field_name => array $handers)
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of WPAPIAdapter\Core\FieldHandlerRepository)
	 */
	public function get_field_handler_repository_mock( array $field_handlers = array() ) {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\Core\FieldHandlerRepository' )
			->disableOriginalConstructor()
			->getMock();

		if ( ! empty( $field_handlers ) ) {
			$mock->expects( $this->any() )
				->method( 'get_handlers' )
				->willReturnCallback(
					function( $name ) use ( $field_handlers ) {
						return isset( $field_handlers[ $name ] )
							? $field_handlers[ $name ]
							: array();
					}
				);
		}

		return $mock;
	}

	/**
	 * @param array $field_handlers ( string $field_name => array $handlers )
	 * @return \WPAPIAdapter\Core\FieldHandlerRepository (Mock)
	 */
	public function get_field_repository_mock( array $field_handlers = array() ) {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\Core\FieldHandlerRepository' )
			->disableOriginalConstructor()
			->getMock();

		if ( ! empty( $field_handlers ) ) {
			$mock->expects( $this->any() )
				->method( 'get_handlers' )
				->willReturnCallback(
					function( $name ) use ( $field_handlers ) {
						return isset( $field_handlers[ $name ] )
							? $field_handlers[ $name ]
							: NULL;
					}
				);
			$mock->expects( $this->any() )
				->method( 'get_fields_to_handle' )
				->willReturn( array_keys( $field_handlers ) );
			$mock->expects( $this->any() )
				->method( 'get_all_handlers_flat' )
				->willReturnCallback(
					function() use ( $field_handlers ) {
						$all_handlers = array();
						foreach ( $field_handlers as $handlers )
							$all_handlers = array_merge( $all_handlers, $handlers );

						return $all_handlers;
					}
			);
		} else {
			$mock->expects( $this->any() )
				->method( 'get_fields_to_handle' )
				->willReturn( array() );
		}

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of \WP_JSON_Server)
	 */
	public function get_json_server_mock() {

		$mock = $this->getMockBuilder( '\WP_JSON_Server' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of \WP_JSON_Response)
	 */
	public function get_json_response_mock() {

		$mock = $this->getMockBuilder( '\WP_JSON_Response' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of \WPAPIAdapter\Core\EntityFieldsController)
	 */
	public function get_post_fields_controller_mock() {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\Core\PostFieldsController' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of \WPAPIAdapter\Builder\EndpointParserBuilder)
	 */
	public function get_endpoint_parser_builder_mock() {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\Builder\EndpointParserBuilder' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of \WPAPIAdapter\Route\EndpointParser)
	 */
	public function get_endpoint_parser_mock() {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\Route\EndpointParser' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of \WP_Error)
	 */
	public function get_wp_error_mock() {

		$mock = $this->getMockBuilder( '\WP_Error' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of \WPAPIAdapter\Core\EntityFieldsController)
	 */
	public function get_entity_fields_controller_mock() {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\Core\EntityFieldsController' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}
}