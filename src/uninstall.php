<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   MuNeCo
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require( 'inc/constants.php' );

/**
 * Class Uninstall
 */
class Uninstall {

	public function __construct() {
		if ( ! defined( 'MUNECO_TABLE_CONNECTIONS' ) ) {
			return false;
		}

		return true;
	}

	public function drop_table() {
		global $wpdb;
		$sql = "DROP TABLE IF EXISTS " . MUNECO_TABLE_CONNECTIONS;
		$wpdb->query( $sql );
	}

	/**
	 * @return bool
	 */
	public function delete_transients() {
		global $wpdb;
		$query_1 = "DELETE FROM " . $wpdb->sitemeta . " WHERE meta_key LIKE '_site_transient_timeout_MuNeCo_getConnectedPPs_%'";
		$query_2 = "DELETE FROM " . $wpdb->sitemeta . " WHERE meta_key LIKE '_site_transient_MuNeCo_getConnectedPPs_%'";

		$query_3 = "DELETE FROM " . $wpdb->sitemeta . " WHERE meta_key LIKE '_site_transient_timeout_MuNeCo_enabledSites'";
		$query_4 = "DELETE FROM " . $wpdb->sitemeta . " WHERE meta_key LIKE '_site_transient_MuNeCo_enabledSites'";

		$existed   = $wpdb->query( $query_1 );
		$existed_2 = $wpdb->query( $query_2 );
		$existed_3 = $wpdb->query( $query_3 );
		$existed_4 = $wpdb->query( $query_4 );

		return true;
	}

	/**
	 * Delete Site options
	 */
	public function delete_site_options() {
		delete_site_option( 'MuNeCo_activatedModules' );
	}

	/**
	 * Delete Options per Blog
	 */
	public function delete_options_per_blog() {
		foreach ( wp_get_sites() as $site ) {
			switch_to_blog( $site['blog_id'] );
			delete_option( 'MuNeCo_langcode' );
			delete_option( 'MuNeCo_status' );
			restore_current_blog();
		}
	}
}

$uninstall = new Uninstall();
$uninstall->drop_table();
$uninstall->delete_site_options();
$uninstall->delete_options_per_blog();
$uninstall->delete_transients();