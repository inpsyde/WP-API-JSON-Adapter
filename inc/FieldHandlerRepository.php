<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter;
use WPAPIAdapter\Field;

class FieldHandlerRepository {

	/**
	 * @type array
	 */
	private $handler = array();

	/**
	 * @param string $field
	 * @param Field\FieldHandlerInterface $handler
	 */
	public function add_handler( $field, Field\FieldHandlerInterface $handler ) {

		if ( ! isset( $this->handler[ $field ] ) )
			$this->handler[ $field ] = array();

		$this->handler[ $field ][] = $handler;
	}

	/**
	 * @param $field
	 * @return array
	 */
	public function get_handlers( $field ) {

		return isset( $this->handler[ $field ] )
			? $this->handler[ $field ]
			: array();
	}
}
