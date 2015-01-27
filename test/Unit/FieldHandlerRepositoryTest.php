<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter;

class FieldHandlerRepositoryTest extends \PHPUnit_Framework_TestCase {

	public function test_repository() {

		$rename_handler_mock = $this->getMockBuilder( '\WPAPIAdapter\Field\RenameFieldHandler' )
			->disableOriginalConstructor()
			->getMock();

		$unset_handler_mock = $this->getMockBuilder( '\WPAPIAdapter\Field\UnsetFieldHandler' )
			->disableOriginalConstructor()
			->getMock();

		$testee = new WPAPIAdapter\FieldHandlerRepository;
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
}
 