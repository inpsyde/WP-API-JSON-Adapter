<?php # -*- coding: utf-8 -*-

namespace WPAPIAdapter\Test;
use WPAPIAdapter;
use Requisite;

require_once dirname( __DIR__ ) . '/inc/init-requisite.php';
require_once dirname( __DIR__ ) . '/inc/register-autoloding.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// there's a bug in WP_Mock's expectAction()
// for now we use a own stub
require_once __DIR__ . '/Stub/do_action.php';

$requisite = WPAPIAdapter\init_requisite( dirname( __DIR__ ) . '/lib' );
WPAPIAdapter\register_autoloading( dirname( __DIR__ ), $requisite );

$requisite->addRule(
	new Requisite\Rule\NamespaceDirectoryMapper(
		__DIR__ . '/Stub',
		'\\'
	)
);
$requisite->addRule(
	new Requisite\Rule\NamespaceDirectoryMapper(
		__DIR__ . '/TestCase',
		__NAMESPACE__ . '\TestCase'
	)
);
