<?php
/**
 * Control Transients / Cache
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\Model;
/**
 * Class Transient
 * @package MuNeCo\Model
 * @since   1.0.0
 */
final class Transient {
	/**
	 * @var Transient
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Singleton - Pattern
	 * @return Transient
	 * @since 1.0.0
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Delete transients from Database
	 * @return bool
	 * @since 1.0.0
	 */
	public function clearNodes() {
		global $wpdb;
		$query_1 = "DELETE FROM " . $wpdb->sitemeta . " WHERE meta_key LIKE '_site_transient_timeout_MuNeCo_getConnectedPPs_%'";
		$query_2 = "DELETE FROM " . $wpdb->sitemeta . " WHERE meta_key LIKE '_site_transient_MuNeCo_getConnectedPPs_%'";
		$wpdb->query( $query_1 );
		$wpdb->query( $query_2 );

		return true;
	}
}

?>