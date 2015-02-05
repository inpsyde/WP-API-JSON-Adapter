<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter;
use WPAPIAdapter\Core;

class FieldHandlerRepositoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @see FieldHandlerRepository::add_handler()
	 * @see FieldHandlerRepository::get_handlers()
	 */
	public function test_repository() {

		$rename_handler_mock = $this->getMockBuilder( '\WPAPIAdapter\Field\RenameFieldHandler' )
			->disableOriginalConstructor()
			->getMock();

		$unset_handler_mock = $this->getMockBuilder( '\WPAPIAdapter\Field\UnsetFieldHandler' )
			->disableOriginalConstructor()
			->getMock();

		$testee = new WPAPIAdapter\Core\FieldHandlerRepository;
		$testee->add_handler( 'one', $rename_handler_mock );
		$testee->add_handler( 'two', $unset_handler_mock );

		$this->assertSame (
			array( $rename_handler_mock ),
			$testee->get_handlers( 'one' )
		);

		$this->assertSame (
			array( $unset_handler_mock ),
			$testee->get_handlers( 'two' )
		);

		$testee->add_handler( 'one', $unset_handler_mock );
		$this->assertSame(
			array(
				$rename_handler_mock,
				$unset_handler_mock
			),
			$testee->get_handlers( 'one' )
		);
	}

	/**
	 * @see FieldHandlerRepository::get_fields_to_handle()
	 */
	public function test_get_fields_to_handle() {

		$rename_handler_mock = $this->getMockBuilder( '\WPAPIAdapter\Field\RenameFieldHandler' )
			->disableOriginalConstructor()
			->getMock();

		$testee = new WPAPIAdapter\Core\FieldHandlerRepository;
		$testee->add_handler( 'ID', $rename_handler_mock );
		$testee->add_handler( 'title', $rename_handler_mock );
		$testee->add_handler( 'content', $rename_handler_mock );
		$testee->add_handler( 'author_ID', $rename_handler_mock );

		$expected = array(
			'ID',
			'title',
			'content',
			'author_ID'
		);
		$this->assertSame(
			$expected,
			$testee->get_fields_to_handle()
		);
	}

	/**
	 * @dataProvider test_get_all_handlers_flat_provider
	 * @see Core\FieldHanderRepository::get_all_handlers_flat
	 * @param array $field_handlers
	 * @param array $expected
	 */
	public function test_get_all_handlers_flat( array $field_handlers, array $expected ) {

		$rename_handler_mock = $this->getMockBuilder( '\WPAPIAdapter\Field\RenameFieldHandler' )
			->disableOriginalConstructor()
			->getMock();

		$testee = new WPAPIAdapter\Core\FieldHandlerRepository;
		foreach ( $field_handlers as $field => $handlers )
			foreach ( $handlers as $handler )
				$testee->add_handler( $field, $handler );

		$this->assertEquals(
			$expected,
			$testee->get_all_handlers_flat()
		);
	}

	/**
	 * @see test_get_all_handlers_flat
	 * @return array
	 */
	public function test_get_all_handlers_flat_provider() {

		$data = array();
		$self = $this;
		$get_mock = function() use( $self ) {
			return $self->getMockBuilder( '\WPAPIAdapter\Field\RenameFieldHandler' )
				->disableOriginalConstructor()
				->getMock();
		};

		#0:
		$mocks = array(
			$get_mock(),
			$get_mock(),
			$get_mock(),
			$get_mock()
		);
		$data[] = array(
			#1. Prameter $handlers
			array(
				'ID'    => array( $mocks[ 0 ], $mocks[ 1 ] ),
				'name'  => array( $mocks[ 2 ] ),
				'title' => array( $mocks[ 3 ] )
			),
			#2. Parameter $expected
			$mocks
		);

		#1:
		$shared_handler = $get_mock();
		$mocks = array(
			$get_mock(),
			$shared_handler,
			$shared_handler,
			$get_mock(),
			$get_mock(),
			$get_mock()
		);
		$data[] = array(
			#1. Prameter $handlers
			array(
				'ID'    => array( $mocks[ 0 ], $mocks[ 1 ] ),
				'name'  => array( $mocks[ 2 ], $mocks[ 3 ], $mocks[ 4 ] ),
				'title' => array( $mocks[ 5 ] )
			),
			#2. Parameter $expected
			$mocks
		);

		#2:
		$mocks = array();
		$data[] = array(
			#1. Parameter $handlers,
			array(),
			#2. Parameter $expected
			$mocks
		);

		return $data;
 	}
}
 