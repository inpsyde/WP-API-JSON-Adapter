<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;
use WPAPIAdapter\Field;

class FieldHandlerRepository {

	/**
	 * @type array
	 */
	private $handlers = array();

	/**
	 * @param string $field
	 * @param Field\FieldHandlerInterface $handler
	 */
	public function add_handler( $field, Field\FieldHandlerInterface $handler ) {

		if ( ! isset( $this->handlers[ $field ] ) )
			$this->handlers[ $field ] = array();

		$this->handlers[ $field ][] = $handler;
	}

	/**
	 * @param $field
	 * @return array
	 */
	public function get_handlers( $field ) {

		return isset( $this->handlers[ $field ] )
			? $this->handlers[ $field ]
			: array();
	}

	/**
	 * @return array
	 */
	public function get_fields_to_handle() {

		return array_keys( $this->handlers );
	}
}
