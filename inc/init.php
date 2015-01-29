<?php # -*- coding: utf-8 -*-

namespace WPAPIAdapter;

/**
 * setup the autoloading and initialize the plugin main object
 *
 * @param string $dir (Optional plugin directory)
 * @wp-hook wp_loaded
 */
function init( $dir = '' ) {

	if ( ! $dir )
		$dir = dirname( __DIR__ );

	$requisite = init_requisite( $dir . '/lib' );
	register_autoloading( $dir, $requisite );

	$plugin = new WPAPIAdapter();
	$plugin->run();
}