<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;
use WPAPIAdapter;
use WPAPIAdapter\Iterator;

class PostFieldsController implements FieldsControllerInterface {

	/**
	 * repository for handlers to change fields
	 *
	 * @type WPAPIAdapter\FieldHandlerRepository
	 */
	private $change_repository;

	/**
	 * repository for handlers to add fields
	 *
	 * @type WPAPIAdapter\FieldHandlerRepository
	 */
	private $add_repository;

	/**
	 * @type bool
	 */
	private $is_single_entity;

	/**
	 * @param WPAPIAdapter\FieldHandlerRepository $change_repository
	 * @param WPAPIAdapter\FieldHandlerRepository $add_repository
	 */
	function __construct(
		WPAPIAdapter\FieldHandlerRepository $change_repository,
		WPAPIAdapter\FieldHandlerRepository $add_repository
	) {

		$this->change_repository = $change_repository;
		$this->add_repository    = $add_repository;
	}

	/**
	 * @param \WP_JSON_Response $response
	 * @return void
	 */
	public function dispatch( \WP_JSON_Response $response ) {

		/**
		 * @param WPAPIAdapter\FieldHandlerRepository
		 */
		do_action( 'wpapiadapter_register_post_change_field_handler', $this->change_repository );

		/**
		 * @param WPAPIAdapter\FieldHandlerRepository
		 */
		do_action( 'wpapiadapter_register_post_add_field_handler',    $this->add_repository );

		// apply change handlers
		if ( $this->is_single_entity )
			$data_iterator = new \ArrayIterator( array( $response->get_data() ) );
		else
			$data_iterator = new \ArrayIterator( $response->get_data() );

		while ( $data_iterator->valid() ) {
			// ignore non-object and non-arrays
			if ( is_scalar( $data_iterator->current() ) )
				$data_iterator->next();

			$key = $data_iterator->key();
			// make the entity an object
			$data_iterator->offsetSet(
				$key,
				$this->entity_to_object( $data_iterator->current() )
			);

			$this->iterate_entity( $data_iterator->current() );
			$data_iterator->next();
		}

		// Todo: that double handling of $this->is_single_entity is not really elegant
		if ( $this->is_single_entity )
			$response->set_data( current( $data_iterator->getArrayCopy() ) );
		else
			$response->set_data( $data_iterator->getArrayCopy() );
	}

	/**
	 * iterate over each field of the entity
	 *
	 * @param \stdClass $entity
	 */
	private function iterate_entity( \stdClass $entity ) {

		$entity_iterator = new Iterator\EntityIterator( $entity, $this->change_repository );
		while ( $entity_iterator->valid() ) {
			$entity_iterator->process_field();
			$entity_iterator->next();
		};
		$this->attach_new_fields( $entity, $entity_iterator );
	}

	/**
	 * Tells the controller to handle the data from WP_JSON_Response
	 * as single entity instead of an array of entities
	 *
	 * @param bool $is_single
	 *
	 * @return mixed
	 */
	public function set_single_entity( $is_single ) {

		$this->is_single_entity = (bool) $is_single;
	}

	/**
	 * @param \stdClass    $entity
	 * @param \ArrayAccess $entity_iterator
	 */
	private function attach_new_fields( \stdClass $entity, \ArrayAccess $entity_iterator ) {

		foreach ( $this->add_repository->get_fields_to_handle() as $field ) {
			foreach ( $this->add_repository->get_handlers( $field ) as $handler ) {
				if ( $entity_iterator->offsetExists( $handler->get_name() ) )
					continue; // Todo: thinking about error handling. Not sure it's worth an Exeption.

				$entity_iterator->offsetSet(
					$handler->get_name(),
					$handler->get_value()
				);
			}
		}
	}

	/**
	 * @param array|object $entity
	 * @return bool|\stdClass
	 */
	private function entity_to_object( $entity ) {

		if ( is_object( $entity ) )
			return $entity;
		elseif ( is_array( $entity ) )
			return (object) $entity;

		return FALSE;
	}
}