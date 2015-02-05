<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter;
use WPAPIAdapter\Test\TestCase;

class EntityFieldsIteratorTest extends TestCase\MockCollectionTestCase {

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

		$testee = new WPAPIAdapter\Iterator\EntityFieldsIterator( $entity, $repo_mock );

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

		$testee = new WPAPIAdapter\Iterator\EntityFieldsIterator( $entity, $repo_mock );

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
	 * test a combination of more than one handler
	 */
	public function test_rename_and_remove_fields() {

		$entity = $this->get_test_entiy();
		$entity_array = get_object_vars( $entity );
		$keys = array_keys( $entity_array );

		// to unset a field, the handler has to return an empty string on get_name()
		// we want to rename author to author_ID
		$unset_status_mock = $this->get_rename_field_handler_mock();
		$unset_status_mock->expects( $this->any() )
			->method( 'get_name' )
			->willReturn( '' );

		// restructure the field "author" (rename it to author_ID and make the value an integer)
		$update_author_mock = $this->get_rename_field_handler_mock();
		$update_author_mock->expects( $this->any() )
			->method( 'get_name' )
			->willReturn( 'author_ID' );
		$update_author_mock->expects( $this->any() )
			->method( 'get_value' )
			->willReturn( 12 );

		$field_handlers = array(
			'status' => array( $unset_status_mock ),
			'author' => array( $update_author_mock )
		);
		$repo_mock = $this->get_field_handler_repository_mock( $field_handlers );

		$testee = new WPAPIAdapter\Iterator\EntityFieldsIterator( $entity, $repo_mock );

		// the iteration
		while( $testee->valid() ) {
			$testee->process_field();
			$testee->next();
		};

		// check existence of any key except 'status' and 'author'
		foreach ( $keys as $key ) {
			switch ( $key ) {
				case 'status' :
				case 'author' :
					$this->assertObjectNotHasAttribute(
						$key,
						$entity
					);
					break;
				default :
					$this->assertObjectHasAttribute(
						$key,
						$entity
					);
					// check data consistency
					$this->assertEquals(
						$entity_array[ $key ],
						$entity->{ $key }
					);
					break;
			}
		}

		// test the renamed key
		$this->assertObjectHasAttribute(
			'author_ID',
			$entity
		);
		$this->assertSame(
			12,
			$entity->author_ID
		);
	}

	/**
	 * does multiple field handlers acts like expect?
	 */
	public function test_multiple_handlers_for_one_field() {

		$entity = $this->get_test_entiy();
		$entity_array = get_object_vars( $entity );
		$keys = array_keys( $entity_array );

		// we want to rename author to author_ID
		$update_status_mock = $this->get_rename_field_handler_mock();
		$update_status_mock->expects( $this->any() )
			->method( 'get_name' )
			->willReturn( 'status' );
		$update_status_mock->expects( $this->any() )
			->method( 'get_value' )
			->willReturn(
				array( 'privious_status' => 'publish', 'new_status' => 'private' )
		);

		$update_status_mock_2 = $this->get_rename_field_handler_mock();
		$update_status_mock_2->expects( $this->any() )
			->method( 'get_name' )
			->willReturn( 'status' );
		$update_status_mock_2->expects( $this->any() )
			->method( 'get_value' )
			->willReturn( 'publish' );

		$update_status_mock_3 = $this->get_rename_field_handler_mock();
		$update_status_mock_3->expects( $this->any() )
			->method( 'get_name' )
			->willReturn( 'status' );
		$update_status_mock_3->expects( $this->any() )
			->method( 'get_value' )
			->willReturn( NULL );


		$field_handlers = array(
			'status' => array(
				$update_status_mock,
				$update_status_mock_2,
				$update_status_mock_3,
			)
		);
		$repo_mock = $this->get_field_handler_repository_mock( $field_handlers );

		$testee = new WPAPIAdapter\Iterator\EntityFieldsIterator( $entity, $repo_mock );

		// the iteration
		while( $testee->valid() ) {
			$testee->process_field();
			$testee->next();
		};

		// check existence of any key except 'status' and 'author'
		foreach ( $keys as $key ) {
			$this->assertObjectHasAttribute(
				$key,
				$entity
			);
			switch ( $key ) {
				case 'status' :
					$this->assertNull(
						$entity->status
					);
					break;
				default :
					// check data consistency
					$this->assertEquals(
						$entity_array[ $key ],
						$entity->{ $key }
					);
					break;
			}
		}
	}

	/**
	 * test whether the iterator also invokes dynamically changed (new) keys
	 */
	public function test_iteration_of_dynamic_keys() {
		$entity = $this->get_test_entiy();
		$entity_array = get_object_vars( $entity );
		$keys = array_keys( $entity_array );



		// rename the author field to author_ID
		$rename_author_mock = $this->get_rename_field_handler_mock();
		$rename_author_mock->expects( $this->any() )
			->method( 'get_name' )
			->willReturn( 'author_ID' );
		$rename_author_mock->expects( $this->any() )
			->method( 'get_value' )
			->willReturn( $entity_array[ 'author' ] );

		// restructure the new author_ID field
		// rename the author field to author_ID
		$restructure_author_mock = $this->get_rename_field_handler_mock();
		$restructure_author_mock->expects( $this->any() )
			->method( 'get_name' )
			->willReturn( 'author_ID' );
		// be sure, the first handler did not cause the change of the structure
		$restructure_author_mock->expects( $this->any() )
			->method( 'handle' )
			->with( $entity_array[ 'author' ]  );
		$restructure_author_mock->expects( $this->any()  )
			->method( 'get_value' )
			->willReturn( 12 );

		$field_handlers = array(
			'author' => array( $rename_author_mock ),
			'author_ID' => array( $restructure_author_mock )
		);
		$repo_mock = $this->get_field_handler_repository_mock( $field_handlers );

		$testee = new WPAPIAdapter\Iterator\EntityFieldsIterator( $entity, $repo_mock );

		// the iteration
		while( $testee->valid() ) {
			$testee->process_field();
			$testee->next();
		};

		// check existence of any key except 'status' and 'author'
		foreach ( $keys as $key ) {
			switch ( $key ) {
				case 'author' :
					$this->assertObjectNotHasAttribute(
						$key,
						$entity
					);
					break;
				default :
					$this->assertObjectHasAttribute(
						$key,
						$entity
					);
					// check data consistency
					$this->assertEquals(
						$entity_array[ $key ],
						$entity->{ $key }
					);
					break;
			}
		}

		$this->assertObjectHasAttribute(
			'author_ID',
			$entity
		);

		$this->assertSame(
			12,
			$entity->author_ID
		);
	}
	/**
	 * @return object
	 */
	public function get_test_entiy() {

		return (object) array(
			'ID'      => 42,
			'title'   => 'My test object',
			'content' => 'Some content in here',
			'status'  => 'publish',
			'author'  => array(
				'ID' => 12,
				'name' => 'John'
			)
		);
	}
}
 