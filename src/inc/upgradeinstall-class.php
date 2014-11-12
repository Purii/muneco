<?php
/**
 * Fired when the plugin is installed.
 *
 * @package   MuNeCo
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
if ( ! defined( 'MUNECO_INSTALLATION' ) ) {
	exit;
}

/**
 * Class MuNeCo_UpgradeInstall
 * @package MuNeCo
 */
class MuNeCo_UpgradeInstall {

	/**
	 * @return bool
	 */
	private function create_connectiontable() {
		if ( ! defined( 'MUNECO_TABLE_CONNECTIONS' ) ) {
			return false;
		}

		global $wpdb;
		$sql = "CREATE TABLE IF NOT EXISTS " . MUNECO_TABLE_CONNECTIONS . " (
		id bigint(200) NOT NULL AUTO_INCREMENT,
		bid bigint(20) DEFAULT '0' NOT NULL,
		pid bigint(20) DEFAULT '0' NOT NULL,
		cbid bigint(20) DEFAULT '0' NOT NULL,
		cpid bigint(20) DEFAULT '0' NOT NULL,
		UNIQUE KEY id (id)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		return true;
	}

	/**
	 * @return bool
	 */
	public function action() {
		return $this->create_connectiontable();
	}
}