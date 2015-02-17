<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Builder;
use WPAPIAdapter\Core;
use WPAPIAdapter\Core\FieldHandlerRepository;

class FieldsControllerBuilder {

	/**
	 * Builds a Core\PostFieldsController with two FieldHandlerRepository objects
	 * inside.
	 *
	 *
	 *
	 * @return Core\PostFieldsController
	 */
	public function build_post_fields_controller() {

		$repos = $this->get_new_repositorys();
		$entity_controller = new Core\EntityFieldsController(
			$repos[ 'change' ],
			$repos[ 'add' ]
		);
		// the Core\PostFieldsController is a decorator for Core\EntityFieldsController
		$controller = new Core\PostFieldsController(
			$entity_controller,
			$repos[ 'change' ],
			$repos[ 'add' ]
		);

		return $controller;
	}

	/**
	 * return an associative array of two
	 * FieldHandlerRepository objects, one for handlers to change fields
	 * one for handlers to add fields
	 *
	 * @return array
	 */
	private function get_new_repositorys() {

		return array(
			'add'    => new FieldHandlerRepository,
			'change' => new FieldHandlerRepository
		);
	}
} 