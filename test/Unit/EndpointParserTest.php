<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Test\Unit;
use WPAPIAdapter\Test\TestCase;
use WPAPIAdapter\Route;

class EndpointParserTest extends TestCase\MockCollectionTestCase {

	/**
	 * @dataProvider get_entity_provider()
	 * @see Route\EntpointParser::get_entity()
	 * @param string $url
	 * @param array $expected
	 */
	public function test_get_entity( $url, array $expected ) {

		$server = new \WP_JSON_Server;
		$server->path = $url;
		$testee = new Route\EndpointParser( $server );

		$this->assertEquals(
			$expected[ 'entity' ],
			$testee->get_entity()
		);
		$this->assertEquals(
			$expected[ 'is_single' ],
			$testee->is_single()
		);
	}

	/**
	 * @see test_get_entity()
	 * @return array
	 */
	public function get_entity_provider() {

		return array(
			#0:
			array(
				# 1.parameter $path
				'/posts/123',
				# 2.parameter $expected
				array(
					'is_single' => TRUE,
					'entity'    => 'posts'
				)
			),
			#1:
			array(
				# 1.parameter $path
				'/posts',
				# 2.parameter $expected
				array(
					'is_single' => FALSE,
					'entity'    => 'posts'
				)
			),
			#2:
			array(
				# 1.parameter $path
				'/users',
				# 2.parameter $expected
				array(
					'is_single' => FALSE,
					'entity'    => 'users'
				)
			),
			#3:
			array(
				# 1.parameter $path
				'/users/5',
				# 2.parameter $expected
				array(
					'is_single' => TRUE,
					'entity'    => 'users'
				)
			),
			#4:
			array(
				# 1.parameter $path
				'/menus',
				# 2.parameter $expected
				array(
					'is_single' => FALSE,
					'entity'    => 'menus'
				)
			),
			#4:
			array(
				# 1.parameter $path
				'/foo',
				# 2.parameter $expected
				array(
					'is_single' => FALSE,
					'entity'    => 'unknown_entity'
				)
			)
		);
	}
}
 