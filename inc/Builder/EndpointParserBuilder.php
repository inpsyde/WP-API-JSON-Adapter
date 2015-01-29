<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Builder;
use WPAPIAdapter\Route;

class EndpointParserBuilder {

	public function build_endpoint_parser( \WP_JSON_Server $server ) {

		return new Route\EndpointParser( $server );
	}
} 