<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Core;
use WPAPIAdapter\Field;

interface FieldProcessorInterface {

	/**
	 * @return bool (Processed or not)
	 */
	public function process_field();
}