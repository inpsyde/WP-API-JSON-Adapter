<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\TestCase;
use WPAPIAdapter;

class MockCollectionTestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * @param \stdClass $entity
	 * @param string      $name
	 * @param mixed      $value
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of WPAPIAdapter\Field\RenameFieldHandler)
	 */
	public function get_rename_field_handler_mock( \stdClass $entity = NULL, $name = NULL, $value = NULL ) {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\Field\RenameFieldHandler' )
			->disableOriginalConstructor()
			->getMock();
		$mock->expects( $this->any() )
			->method( 'handle' )
			->willReturn( NULL );

		if ( $name ) {
			$mock->expects( $this->any() )
				->method( 'get_name' )
				->willReturn( $name );
		}
		if ( $value ) {
			$mock->expects( $this->any() )
				->method( 'get_value' )
				->willReturn( $value );
		}

		return $mock;
	}

	/**
	 * @param array $field_handlers (str $field_name => array $handers)
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject (Mock of WPAPIAdapter\FieldHandlerRepository)
	 */
	public function get_field_handler_repository_mock( array $field_handlers = array() ) {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\FieldHandlerRepository' )
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
	 * @return WPAPIAdapter\FieldHandlerRepository (Mock)
	 */
	public function get_field_repository_mock( array $field_handlers = array() ) {

		$mock = $this->getMockBuilder( '\WPAPIAdapter\FieldHandlerRepository' )
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
		} else {
			$mock->expects( $this->any() )
				->method( 'get_fields_to_handle' )
				->willReturn( array() );
		}

		return $mock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function get_json_server_mock() {

		$mock = $this->getMockBuilder( '\WP_JSON_Server' )
			->disableOriginalConstructor()
			->getMock();

		return $mock;
	}
} 