<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Builder;
use WPAPIAdapter\Core;
use WPAPIAdapter\Core\FieldHandlerRepository;

class FieldsControllerBuilder {

	public function build_post_fields_controller() {

		$repos = $this->get_new_repositorys();
		$entity_controller = new Core\EntityFieldsController(
			$repos[ 'change' ],
			$repos[ 'add' ]
		);
		$controller = new Core\PostFieldsController(
			$entity_controller,
			$repos[ 'change' ],
			$repos[ 'add' ]
		);

		return $controller;
	}

	/**
	 * @return array
	 */
	private function get_new_repositorys() {

		return array(
			'add'    => new FieldHandlerRepository,
			'change' => new FieldHandlerRepository
		);
	}
} 