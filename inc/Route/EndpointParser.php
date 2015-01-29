<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Route;

/**
 * Defines the entity requested by an endpoint
 *
 * @package WPAPIAdapter\Route
 */
class EndpointParser {

	/**
	 * @type \WP_JSON_Server
	 */
	private $server;

	/**
	 * @type string (always plural. e.g.: posts, users, terms, etc.)
	 */
	private $entity;

	/**
	 * @type string
	 */
	private $unknown_entity_slug;

	/**
	 * @type bool
	 */
	private $is_single_entity = FALSE;

	/**
	 * @param \WP_JSON_Server $server
	 */
	function __construct( \WP_JSON_Server $server ) {

		$this->server = $server;
		$this->unknown_entity_slug = 'unknown_entity';
	}

	/**
	 * @return string
	 */
	public function get_entity() {

		if ( empty( $this->entity ) )
			$this->parse();

		return $this->entity;
	}

	/**
	 * @return bool
	 */
	public function is_single() {

		return $this->is_single_entity;
	}

	/**
	 * checks the request URI and determines the requested entity
	 */
	private function parse() {

		$path = ltrim( $this->server->path, '/' );
		$segments = explode( '/', $path );

		if ( empty( $segments[ 0 ] ) ) {
			$this->entity = $this->unknown_entity_slug;
			return;
		}

		if ( in_array( $segments[ 0 ], $this->get_entites() ) ) {
			$this->entity = $segments[ 0 ];
		} else {
			$this->entity = $this->unknown_entity_slug;
			return;
		}

		if ( isset( $segments[ 1 ] ) && is_numeric( $segments[ 1 ] ) )
			$this->is_single_entity = TRUE;
		else
			$this->is_single_entity = FALSE;
	}

	/**
	 * @return array
	 */
	private function get_entites() {

		$entities = array(
			'posts',
			'users',
			'terms',
			'menus'
		);

		return $entities;
	}
}