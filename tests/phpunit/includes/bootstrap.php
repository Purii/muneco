<?php


$wp_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $wp_tests_dir ) {
	$wp_tests_dir = '/tmp/wordpress-tests-lib';
}
require_once $wp_tests_dir . '/includes/functions.php';
function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../muneco.php';
	perform_activation();
}

require $_tests_dir . '/includes/bootstrap.php';


/* Local

define( 'WP_TESTS_DIR', '/home/patrick/wordpress_testarea/wordpresscore/tests/phpunit' );
define( 'PLUGIN_DIRECTORY', '/home/patrick/wordpress_testarea/plugins/muneco/src' );

require_once WP_TESTS_DIR . '/includes/functions.php';

function _manually_load_plugin() {
    require PLUGIN_DIRECTORY . '/muneco.php';
    perform_activation();
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require WP_TESTS_DIR . '/includes/bootstrap.php';

*/