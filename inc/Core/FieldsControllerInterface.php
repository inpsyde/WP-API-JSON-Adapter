<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;


interface FieldsControllerInterface {

	/**
	 * @param \WP_JSON_Response $response
	 * @return void
	 */
	public function dispatch( \WP_JSON_Response $response );

	/**
	 * Tells the controller to handle the data from WP_JSON_Response
	 * as single entity instead of an array of entities
	 *
	 * @param bool $is_single
	 * @return void
	 */
	public function set_single_entity( $is_single );

	/**
	 * @param \WP_JSON_Server $server
	 * @return mixed
	 */
	public function set_json_server( \WP_JSON_Server $server );
} 