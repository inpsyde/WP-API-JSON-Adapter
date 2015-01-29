<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;
use WPAPIAdapter\Core;
use WPAPIAdapter\Route;
use WPAPIAdapter\Builder;

class DataShaper {

	/**
	 * @type array ( string $entity => Core\FieldControllerInterface )
	 */
	private $fields_controller;

	/**
	 * @type Builder\EndpointParserBuilder
	 */
	private $parser_builder;

	function __construct( Builder\EndpointParserBuilder $parser_builder ) {

		$this->parser_builder = $parser_builder;
	}

	/**
	 * @wp-hook json_pre_dispatch
	 * @param mixed $response
	 * @param \WP_JSON_Server $server
	 * @return \WP_JSON_Response
	 */
	public function shape_data( $response, \WP_JSON_Server $server ) {

		$parser = $this->parser_builder->build_endpoint_parser( $server );
		$entity = $parser->get_entity();
		if ( empty( $response ) )
			$response = $server->dispatch();

		if ( empty( $this->fields_controller[ $entity ] ) || is_wp_error( $response ) )
			return $response;


		$response = $this->sanitize_response( $response );

		$this->fields_controller[ $entity ]->dispatch( $response );

		return $response;
	}

	/**
	 * @param string $entity
	 * @param Core\FieldControllerInterface $controller
	 */
	public function add_entity_controller( $entity, Core\FieldControllerInterface $controller ) {

		$this->fields_controller[ $entity ] = $controller;
	}

	/**
	 * @param mixed $response
	 * @return \WP_JSON_Response
	 */
	public function sanitize_response( $response ) {

		if ( is_a( $response, '\WP_JSON_ResponseInterface' ) )
			return $response;
	    else
			return new \WP_JSON_Response( $response );
	}
}