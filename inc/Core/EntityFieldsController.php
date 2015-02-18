<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;
use WPAPIAdapter;
use WPAPIAdapter\Iterator;
use WPAPIAdapter\Field;

/**
 * Class EntityFieldsController
 *
 * General Controller for any entity type (post, user, term).
 * Iterates over each entity object in the set of the response and
 * apply an Iterator\EntityFieldsIterator.
 *
 * @package WPAPIAdapter\Core
 */
class EntityFieldsController implements FieldsControllerInterface {

	/**
	 * repository for handlers to change fields
	 *
	 * @type \WPAPIAdapter\Core\FieldHandlerRepository
	 */
	private $change_repository;

	/**
	 * repository for handlers to add fields
	 *
	 * @type \WPAPIAdapter\Core\FieldHandlerRepository
	 */
	private $add_repository;

	/**
	 * @type bool
	 */
	private $is_single_entity;

	/**
	 * @type \WP_JSON_Server
	 */
	private $server;

	/**
	 * @param \WPAPIAdapter\Core\FieldHandlerRepository $change_repository
	 * @param \WPAPIAdapter\Core\FieldHandlerRepository $add_repository
	 */
	function __construct(
		WPAPIAdapter\Core\FieldHandlerRepository $change_repository,
		WPAPIAdapter\Core\FieldHandlerRepository $add_repository
	) {

		$this->change_repository = $change_repository;
		$this->add_repository    = $add_repository;
	}

	/**
	 * @param \WP_JSON_Response $response
	 * @return void
	 */
	public function dispatch( \WP_JSON_Response $response ) {

		// deploy the server to all registered handlers
		if ( $this->server )
			$this->deploy_server_to_handlers( $this->server );

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

		$entity_iterator = new Iterator\EntityFieldsIterator( $entity, $this->change_repository );
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
	 * @return void
	 */
	public function set_single_entity( $is_single ) {

		$this->is_single_entity = (bool) $is_single;
	}

	/**
	 * deploy the JSON-Server to each handler.
	 * (smells a bit like the courier anti-pattern)
	 *
	 * @param \WP_JSON_Server $server
	 * @return void
	 */
	private function deploy_server_to_handlers( \WP_JSON_Server $server ) {

		/* @type Field\FieldHandlerInterface $handler */
		foreach ( $this->add_repository->get_all_handlers_flat() as $handler )
			$handler->set_server( $server );

		foreach ( $this->change_repository->get_all_handlers_flat() as $handler )
				$handler->set_server( $server );
	}

	/**
	 * @param \WP_JSON_Server $server
	 * @return mixed
	 */
	public function set_json_server( \WP_JSON_Server $server ) {

		$this->server = $server;
	}

	/**
	 * @param \stdClass    $entity
	 * @param \ArrayAccess $entity_iterator
	 */
	private function attach_new_fields( \stdClass $entity, \ArrayAccess $entity_iterator ) {

		$original_entity = clone $entity;
		foreach ( $this->add_repository->get_fields_to_handle() as $field ) {
			foreach ( $this->add_repository->get_handlers( $field ) as $handler ) {
				/* @type Field\FieldHandlerInterface $handler */
				$handler->set_original_entity( clone $original_entity );
				if ( $entity_iterator->offsetExists( $handler->get_name() ) )
					continue; // Todo: thinking about error handling. Not sure it's worth an Exeption.

				$handler->handle( NULL ); // call this method to treat add/change handler the same way
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
	public function entity_to_object( $entity ) {

		if ( is_object( $entity ) )
			return $entity;
		elseif ( is_array( $entity ) )
			return (object) $entity;

		return FALSE;
	}
}