<?php
/**
 * Set Contants
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
if ( ! defined( 'MUNECO_VERSION' ) ) {
	define( 'MUNECO_VERSION', '0.1' );
}
if ( ! defined( 'MUNECO_BASENAME' ) ) {
	define( 'MUNECO_BASENAME', plugin_basename( dirname( __FILE__ ) ) );
}
if ( ! defined( 'MUNECO_BASEURL' ) ) {
	define( 'MUNECO_BASEURL', plugin_dir_url( dirname( __FILE__ ) ) );
}
if ( ! defined( 'MUNECO_BASEPATH' ) ) {
	define( 'MUNECO_BASEPATH', plugin_dir_path( dirname( __FILE__ ) ) );
}
if ( ! defined( 'MUNECO_COREMODULESPATH' ) ) {
	define( 'MUNECO_COREMODULESPATH', MUNECO_BASEPATH . 'coremodules/' );
}
if ( ! defined( 'MUNECO_MODULESPATH' ) ) {
	define( 'MUNECO_MODULESPATH', MUNECO_BASEPATH . 'modules/' );
}
if ( ! defined( 'MUNECO_INCSPATH' ) ) {
	define( 'MUNECO_INCSPATH', MUNECO_BASEPATH . 'inc/' );
}
if ( ! defined( 'MUNECO_MODELPATH' ) ) {
	define( 'MUNECO_MODELPATH', MUNECO_BASEPATH . 'model/' );
}
if ( ! defined( 'MUNECO_TABLE_CONNECTIONS' ) ) {
	global $wpdb;
	define( 'MUNECO_TABLE_CONNECTIONS', $wpdb->base_prefix . 'muneco_connections' );
}