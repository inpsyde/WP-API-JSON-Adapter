<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter;

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
}
 