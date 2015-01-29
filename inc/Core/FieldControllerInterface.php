<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;


interface FieldControllerInterface {

	/**
	 * @param \WP_JSON_Response $response
	 * @return mixed
	 */
	public function dispatch( \WP_JSON_Response $response );

	/**
	 * Tells the controller to handle the data from WP_JSON_Response
	 * as single entity instead of an array of entities
	 *
	 * @param bool $is_single
	 * @return mixed
	 */
	public function set_single_entity( $is_single );
} 