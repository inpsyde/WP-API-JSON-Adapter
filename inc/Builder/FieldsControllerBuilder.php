<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Builder;
use WPAPIAdapter\Core;
use WPAPIAdapter\Core\FieldHandlerRepository;

class FieldsControllerBuilder {

	/**
	 * Builds a Core\PostFieldsController with two FieldHandlerRepository objects
	 * inside.
	 *
	 * @return Core\PostFieldsController
	 */
	public function build_post_fields_controller() {

		$repos = $this->get_new_repositories();
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
	 * Builds a Core\UserFieldsController with two FieldHandlerRepository objects
	 * inside.
	 *
	 * @return Core\UserFieldsController
	 */
	public function build_user_fields_controller() {

		$repos = $this->get_new_repositories();
		$entity_controller = new Core\EntityFieldsController(
			$repos[ 'change' ],
			$repos[ 'add' ]
		);

		// the Core\UserFieldsController is a decorator for Core\EntityFieldsController
		$controller = new Core\UserFieldsController(
			$entity_controller,
			$repos[ 'change' ],
			$repos[ 'add' ]
		);

		return $controller;
	}

	/**
	 * Builds a Core\TermFieldsController with two FieldHandlerRepository objects
	 * inside.
	 *
	 * @return Core\TermFieldsController
	 */
	public function build_term_fields_controller() {

		$repos = $this->get_new_repositories();
		$entity_controller = new Core\EntityFieldsController(
			$repos[ 'change' ],
			$repos[ 'add' ]
		);

		// the Core\UserFieldsController is a decorator for Core\EntityFieldsController
		$controller = new Core\TermFieldsController(
			$entity_controller,
			$repos[ 'change' ],
			$repos[ 'add' ]
		);

		return $controller;
	}

	/**
	 * Builds a Core\MenuFieldsController with two FieldHandlerRepository objects
	 * inside.
	 *
	 * @return Core\TermFieldsController
	 */
	public function build_menu_fields_controller() {

		$repos = $this->get_new_repositories();
		$entity_controller = new Core\EntityFieldsController(
			$repos[ 'change' ],
			$repos[ 'add' ]
		);

		// the Core\MenuFieldsController is a decorator for Core\EntityFieldsController
		$controller = new Core\MenuFieldsController(
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
	private function get_new_repositories() {

		return array(
			'add'    => new FieldHandlerRepository,
			'change' => new FieldHandlerRepository
		);
	}
} 