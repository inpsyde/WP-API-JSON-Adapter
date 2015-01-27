<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter;
use WPAPIAdapter\Test\TestCase;

class EntityIteratorTest extends TestCase\MockCollectionTestCase {

	/**
	 * @see WPAPIAdapter\Iterator\EntityIterator::process_field()
	 */
	public function test_rename_field() {

		$entity = $this->get_test_entiy();
		$author_ID = $entity->author;

		// to rename a field, the handler has to return the new name on get_name()
		// we want to rename author to author_ID
		$handle_mock = $this->get_rename_field_handler_mock( $entity, 'author_ID', $author_ID );
		// register the mock handler for the field we want to rename 'author'
		$field_handlers = array(
			'author' => array( $handle_mock )
		);
		$repo_mock = $this->get_field_handler_repository_mock( $field_handlers );

		$testee = new WPAPIAdapter\Iterator\EntityIterator( $entity, $repo_mock );

		while( $testee->valid() ) {
			$testee->process_field();
			$testee->next();
		};

		// the old key should be removed
		$this->assertObjectNotHasAttribute(
			'author',
			$entity
		);
		// instead the new should be there
		$this->assertObjectHasAttribute(
			'author_ID',
			$entity
		);

		// the value should still be the same
		$this->assertEquals(
			$author_ID,
			$entity->author_ID
		);
	}

	/**
	 * @see WPAPIAdapter\Iterator\EntityIterator::process_field()
	 */
	public function test_unset_field() {

		$entity = $this->get_test_entiy();
		$entity_array = get_object_vars( $entity );
		$keys = array_keys( $entity_array );

		// to unset a field, the handler has to return an empty string on get_name()
		// we want to rename author to author_ID
		$handle_mock = $this->get_rename_field_handler_mock( $entity );
		$handle_mock->expects( $this->any() )
			->method( 'get_name' )
			->willReturn( '' );
		$field_handlers = array(
			'title' => array( $handle_mock )
		);
		$repo_mock = $this->get_field_handler_repository_mock( $field_handlers );

		$testee = new WPAPIAdapter\Iterator\EntityIterator( $entity, $repo_mock );

		// the iteration
		while( $testee->valid() ) {
			$testee->process_field();
			$testee->next();
		};

		// check existence of any key except 'title'
		foreach ( $keys as $key ) {
			if ( 'title' === $key ) {
				$this->assertObjectNotHasAttribute(
					$key,
					$entity
				);
			} else {
				$this->assertObjectHasAttribute(
					$key,
					$entity
				);
				// check data consistency
				$this->assertEquals(
					$entity_array[ $key ],
					$entity->{ $key }
				);
			}
		}
	}



	/**
	 * @return object
	 */
	public function get_test_entiy() {

		return (object) array(
			'ID'      => 42,
			'title'   => 'My test object',
			'content' => 'Some content in here',
			'author'  => 12
		);
	}
}
 