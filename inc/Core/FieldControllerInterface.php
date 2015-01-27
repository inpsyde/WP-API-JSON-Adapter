<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;


interface FieldControllerInterface {

	public function dispatch( \WP_JSON_Response $response );
} 