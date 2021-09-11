<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Sample_Plugin
 *
 * SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
 * SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

//$_tests_dir = getenv( 'WP_TESTS_DIR' );
require_once dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';
$_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: getenv( 'WP_PHPUNIT__DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?";
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
    define('GIGLOGADMIN_UNIT_TEST', true);
	require dirname( dirname( __FILE__ ) ) . '/giglogadmin.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
