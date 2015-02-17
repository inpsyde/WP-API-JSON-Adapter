<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Builder;
use WPAPIAdapter\Route;

class EndpointParserBuilder {

	/**
	 * Builds a single Route\EndpointParser object with the dependency of
	 * \WP_JSON_Server
	 *
	 * @param \WP_JSON_Server $server
	 *
	 * @return Route\EndpointParser
	 */
	public function build_endpoint_parser( \WP_JSON_Server $server ) {

		return new Route\EndpointParser( $server );
	}
} 