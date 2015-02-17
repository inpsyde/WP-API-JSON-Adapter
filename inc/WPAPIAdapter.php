<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter;

class WPAPIAdapter {

	/**
	 * @type Builder\FieldsControllerBuilder
	 */
	private $fields_ctrl_builder;

	/**
	 * @type Builder\EndpointParserBuilder
	 */
	private $endpoint_parser_builder;

	public function __construct() {

		$this->fields_ctrl_builder = new Builder\FieldsControllerBuilder;
		$this->endpoint_parser_builder = new Builder\EndpointParserBuilder;
	}

	/**
	 * build the DataShaper and place the filter callback
	 */
	public function run() {

		$data_shaper = new Core\DataShaper( $this->endpoint_parser_builder );
		$data_shaper->add_entity_controller(
			'posts',
			$this->fields_ctrl_builder->build_post_fields_controller()
		);
		$data_shaper->add_entity_controller(
			'users',
			$this->fields_ctrl_builder->build_user_fields_controller()
		);
		$data_shaper->add_entity_controller(
			'terms',
			$this->fields_ctrl_builder->build_term_fields_controller()
		);
		$data_shaper->add_entity_controller(
			'menus',
			$this->fields_ctrl_builder->build_menu_fields_controller()
		);

		// Todo: register a controller for taxonomies

		add_filter( 'json_pre_dispatch', array( $data_shaper, 'shape_data' ), 10, 2 );
	}
} 