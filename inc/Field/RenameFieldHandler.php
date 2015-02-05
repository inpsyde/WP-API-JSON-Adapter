<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Field;


class RenameFieldHandler implements FieldHandlerInterface {

	/**
	 * @type string
	 */
	private $new_name = '';

	/**
	 * @type mixed
	 */
	private $value;

	/**
	 * @param string $new_name
	 */
	function __construct( $new_name ) {

		$this->new_name = (string) $new_name;
	}

	/**
	 * @return string
	 */
	public function get_name() {

		return $this->new_name;
	}

	/**
	 * @return mixed
	 */
	public function get_value() {

		return $this->value;
	}

	/**
	 * @param \WP_JSON_Server $server
	 *
	 * @return void
	 */
	public function set_server( \WP_JSON_Server $server ) {}

	/**
	 * @param $field (any primitive value str,array,\stdClass,…)
	 *
	 * @return void
	 */
	public function handle( $field ) {

		$this->value = $field;
	}

	/**
	 * @param \stdClass $original_entity
	 * @return void
	 */
	public function set_original_entity( \stdClass $original_entity ) {}
}