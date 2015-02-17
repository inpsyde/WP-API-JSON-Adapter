<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;

/**
 * Class PostFieldsController
 *
 * Decorator for the EntityFieldsController to apply post specific
 * filters to the FieldHandlers.
 *
 * @package WPAPIAdapter\Core
 */
class PostFieldsController implements FieldsControllerInterface {

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
		do_action( 'wpapiadapter_register_post_change_field_handler', $this->change_repository );

		/**
		 * @param WPAPIAdapter\Core\FieldHandlerRepository
		 */
		do_action( 'wpapiadapter_register_post_add_field_handler',    $this->add_repository );

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

		$this->controller->set_single_entity( (bool) $is_single );
	}

	/**
	 * @param \WP_JSON_Server $server
	 * @return mixed
	 */
	public function set_json_server( \WP_JSON_Server $server ) {

		$this->controller->set_json_server( $server );
	}
} 