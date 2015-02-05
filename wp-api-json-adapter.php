<?php # -*- coding: utf-8 -*-

/**
 * Plugin Name: WP API JSON adapter
 * Description: A package that allows developers to adapt the structure of the JSON response of the REST API.
 * Plugin URL:
 * Author: Inpsyde GmbH
 * Author URL: http://inpsyde.com
 * Version: 2015.02.05
 */

namespace WPAPIAdapter;

$file_loader = function( $file ) {

	require_once __DIR__ . '/inc/' . $file;
};
$file_loader( 'init-requisite.php' );
$file_loader( 'register-autoloading.php' );
$file_loader( 'init.php' );

add_action( 'wp_loaded', __NAMESPACE__ . '\init' );