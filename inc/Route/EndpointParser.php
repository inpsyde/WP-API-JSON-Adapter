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

		switch ( $this->entity ) {
			case 'posts' :
			case 'users' :
				if ( isset( $segments[ 1 ] ) && is_numeric( $segments[ 1 ] ) )
					$this->is_single_entity = TRUE;
				else
					$this->is_single_entity = FALSE;

				if ( 'users' === $this->entity ) {
					if ( isset( $segments[ 1 ] ) && 'me' === $segments[ 1 ] )
						$this->is_single_entity = TRUE;
				}
				break;

			case 'taxonomies' :

				if ( isset( $segments[ 1 ] ) && ! isset ( $segments[ 2 ] ) ) {
					/* /taxonomies/category */
					$this->is_single_entity = TRUE;
				} elseif ( isset( $segments[ 2 ] ) && 'terms' === $segments[ 2 ] ) {
					/* /taxonomies/category/terms */
					$this->entity = 'terms';
					$this->is_single_entity = FALSE;

					/* /taxonomies/category/terms/12 */
					if ( isset( $segments[ 3 ] ) && is_numeric( $segments[ 3 ] ) )
						$this->is_single_entity = TRUE;
				}
				break;
		}

	}

	/**
	 * @return array
	 */
	private function get_entites() {

		$entities = array(
			'posts',
			'users',
			'terms',
			'menus',
			'taxonomies'
		);

		return $entities;
	}
}