<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Field;


interface FieldHandlerInterface {

	/**
	 * @return string
	 */
	public function get_name();

	/**
	 * @return mixed
	 */
	public function get_value();

	/**
	 * @param \WP_JSON_Server $server
	 * @return void
	 */
	public function set_server( \WP_JSON_Server $server );

	/**
	 * @param $field (any primitive value str,array,\stdClass,…)
	 * @return void
	 */
	public function handle( $field );

	/**
	 * @param \stdClass $original_entity
	 * @return void
	 */
	public function set_original_entity( \stdClass $original_entity );
}