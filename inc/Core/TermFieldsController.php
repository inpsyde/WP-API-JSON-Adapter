<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;

/**
 * Class TermFieldsController
 *
 * Decorator for the EntityFieldsController to apply term specific
 * filters to the FieldHandlersRepositories.
 *
 * @package WPAPIAdapter\Core
 */
class TermFieldsController implements FieldsControllerInterface {

	/**
	 * @type EntityFieldsController
	 */
	private $controller;

	/**
	 * repository for handlers to change fields
	 *
	 * @type FieldHandlerRepository
	 */
	private $change_repository;

	/**
	 * repository for handlers to add fields
	 *
	 * @type FieldHandlerRepository
	 */
	private $add_repository;

	/**
	 * @type \WP_JSON_Server
	 */
	private $server;

	/**
	 * @type string
	 */
	private $taxonomy = '';

	/**
	 * @type string
	 */
	private $request_url;

	/**
	 * @type bool
	 */
	private $is_single_entity = FALSE;

	/**
	 * @param FieldsControllerInterface $controller
	 * @param FieldHandlerRepository  $change_repository
	 * @param FieldHandlerRepository  $add_repository
	 */
	function __construct(
		FieldsControllerInterface $controller,
		FieldHandlerRepository $change_repository,
		FieldHandlerRepository $add_repository
	) {

		$this->controller        = $controller;
		$this->change_repository = $change_repository;
		$this->add_repository    = $add_repository;
	}

	/**
	 * @param \WP_JSON_Response $response
	 * @return void
	 */
	public function dispatch( \WP_JSON_Response $response ) {

		/**
		 * @param WPAPIAdapter\Core\FieldHandlerRepository
		 */
		do_action( 'wpapiadapter_register_term_change_field_handler', $this->change_repository );

		/**
		 * @param WPAPIAdapter\Core\FieldHandlerRepository
		 */
		do_action( 'wpapiadapter_register_term_add_field_handler',    $this->add_repository );

		$this->add_taxonomy_to_term( $response );
		$this->controller->dispatch( $response );
	}

	/**
	 * Tells the controller to handle the data from WP_JSON_Response
	 * as single entity instead of an array of entities
	 *
	 * @param bool $is_single
	 * @return mixed
	 */
	public function set_single_entity( $is_single ) {

		$this->is_single_entity = (bool) $is_single;
		$this->controller->set_single_entity( (bool) $is_single );
	}

	/**
	 * @param \WP_JSON_Server $server
	 * @return mixed
	 */
	public function set_json_server( \WP_JSON_Server $server ) {

		$this->server = $server;
		$this->controller->set_json_server( $server );
	}

	/**
	 * a dirty hack to append the »taxonomy« attribute to the
	 * term objects to allow handlers to get the term from DB
	 *
	 * @param \WP_JSON_Response $response
	 */
	private function add_taxonomy_to_term( \WP_JSON_Response $response ) {

		$data = $response->get_data();
		if ( empty( $data ) )
			return;

		if ( $this->is_single_entity ) {
			$data = $this->controller->entity_to_object( $data );
			if ( ! isset ( $data->taxonomy ) )
				$data->taxonomy = $this->get_taxonomy();
		} else {
			$data_iterator = new \ArrayIterator( $data );
			while ( $data_iterator->valid() ) {
				// ignore non-object and non-arrays
				if ( is_scalar( $data_iterator->current() ) )
					$data_iterator->next();

				$key = $data_iterator->key();
				$entity = $this->controller->entity_to_object( $data_iterator->current() );
				if ( ! isset( $entity->taxonomy ) )
					$entity->taxonomy = $this->get_taxonomy();
				// make the entity an object
				$data_iterator->offsetSet(
					$key,
					$entity
				);

				$data_iterator->next();
			}
			$data = $data_iterator->getArrayCopy();
		}

		$response->set_data( $data );
	}

	/**
	 * @return string
	 */
	public function get_taxonomy() {

		if ( ! $this->server )
			return $this->taxonomy;

		if ( $this->server->path === $this->request_url )
			return $this->taxonomy;

		$path     = ltrim( $this->server->path, '/' );
		$segments = explode( '/', $path );

		if ( ! isset( $segments[ 1 ] ) || ! taxonomy_exists( $segments[ 1 ] ) )
			return $this->taxonomy;

		$this->taxonomy = $segments[ 1 ];
		$this->request_url = $this->server->path;

		return $this->taxonomy;
	}
} 