<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;
use WPAPIAdapter\Field;

interface FieldsProcessorInterface {

	/**
	 * @return bool (Processed or not)
	 */
	public function process_field();
}