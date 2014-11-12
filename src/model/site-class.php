<?php
/**
 * Single Site/Blog
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\Model;
/**
 * Class Site
 * @package MuNeCo\Model
 * @since   1.0.0
 */
final class Site {


	/**
	 * @var int
	 */
	private $blogID;


	public function __construct( $blogID ) {
		$this->blogID = $blogID;
		if ( ! $this->getBlogMeta() ) {
			return false;
		}
	}

	/**
	 * @return boolean | Object
	 */
	public function getBlogMeta() {
		$blogMeta               = get_blog_details( $this->blogID );
		$blogMeta->languagecode = $this->getLanguagecode();
		$blogMeta->munecostatus = $this->getMunecostatus();

		return $blogMeta;
	}


	/**
	 * @return int
	 */
	public function getBlogID() {
		return $this->blogID;
	}


	/**
	 * @param int $blogID
	 *
	 * @return mixed|void
	 * @since 1.0.0
	 */

	public function getLanguagecode() {
		if ( null == $this->blogID ) {
			return get_option( 'muneco_languagecode' );
		}
		switch_to_blog( $this->blogID );
		$languagecode = get_option( 'muneco_languagecode' );
		if ( false == $languagecode ) {
			$languagecode = get_bloginfo( 'language' );
		}
		restore_current_blog();

		return $languagecode;
	}

	/**
	 * @param int $blogID
	 * @param string $langCode
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function setLanguagecode( $languagecode = null ) {
		if ( null == $this->blogID || null == $languagecode ) {
			return false;
		}
		switch_to_blog( $this->blogID );
		$statusUpdate = update_option( 'muneco_languagecode', $languagecode );
		restore_current_blog();

		return $statusUpdate;
	}

	/**
	 * @param int $blogID
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function deleteLanguagecode() {
		if ( null == $this->blogID ) {
			return false;
		}
		switch_to_blog( $this->blogID );
		$statusUpdate = delete_option( 'muneco_languagecode' );
		restore_current_blog();

		return $statusUpdate;
	}

	/**
	 * @param int $blogID
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function getMunecostatus() {
		switch_to_blog( $this->blogID );
		$blogStatus = get_option( 'muneco_status' );
		restore_current_blog();

		if ( $blogStatus == 1 ) {
			return true;
		}

		return false;
	}

	/**
	 *
	 * @param int $blogID
	 * @param int $status
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function setMunecostatus( $status = null ) {
		if ( null == $this->blogID || null == $status ) {
			return false;
		}
		\switch_to_blog( $this->blogID );
		$statusUpdate = \update_option( 'muneco_status', $status );
		\restore_current_blog();

		return $statusUpdate;
	}

	/**
	 * @param int $blogID
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function deleteMunecostatus() {
		if ( null == $this->blogID ) {
			return false;
		}
		\switch_to_blog( $this->blogID );
		$statusUpdate = \delete_option( 'muneco_status' );
		\restore_current_blog();

		return $statusUpdate;
	}


	/**
	 * List all PostsPages by BID which are connected
	 *
	 * @param string $post_type
	 *
	 * @return array: [PID] => array('cbid' => BID, 'cpid' => PID)
	 * @since 1.0.0
	 */
	public function getNodesByBID( $post_type = 'post' ) {
		global $wpdb;

		$blogID = $this->blogID;

		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		$connected = $wpdb->get_results(
			"SELECT pid as pid, cpid as cpid, cbid as cbid FROM " . MUNECO_TABLE_CONNECTIONS . " WHERE bid = $blogID AND cpid != 0",
			"ARRAY_A"
		);
		/*
		 * BIP as Index
		 * Posts can have mulitple connections
		 */
		$connection_sort   = array();
		$network           = new Network();
		$enabledSites      = $network->getMunecoEnabledSites( true );
		$enabledSitesArray = array();
		foreach ( $enabledSites as $enabledSite ) {
			$enabledSitesArray[ $enabledSite->blog_id ] = true;
		}
		foreach ( $connected as $connection ) {
			if ( ! array_key_exists( $connection['cbid'], $enabledSitesArray ) ) {
				continue;
			}
			$singleJunction = new Junction( $connection['cbid'], $connection['cpid'] );
			if ( $post_type != $singleJunction->getJunction()->post_type ) {
				continue;
			}
			if ( ! isset( $connection_sort[ $connection['pid'] ] ) ) {
				$connection_sort[ $connection['pid'] ] = array();
			}
			array_push( $connection_sort[ $connection['pid'] ], array(
				'cbid' => $connection['cbid'],
				'cpid' => $connection['cpid']
			) );

		}

		return $connection_sort;
	}


	/**
	 * List all PostsPages from BID
	 * Appends Connection-Info
	 *
	 * @param bool $only_not_connected
	 * @param string $post_type
	 *
	 * @return array|bool
	 * @since 1.0.0
	 */
	public function getJunctions( $only_not_connected = false, $post_type = 'post' ) {

		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		$blogID = $this->blogID;
		switch_to_blog( $blogID );
		$args = array(
			'post_type'    => $post_type,
			'hierarchical' => 1
		);
		if ( 'post' == $post_type ) {
			$return = get_posts( $args );
		} else {
			$return = get_pages( $args );
		}
		restore_current_blog();
		if ( ! empty( $return ) ) {
			if ( ! $only_not_connected ) {
				return $return;
			}

			/*
			 * Filter out connected
			 */
			$connected_junctions = $this->getNodesByBID( $post_type );
			$returnNotConnected  = array();
			foreach ( $return as $junction ) {
				if ( ! array_key_exists( $junction->ID, $connected_junctions ) ) {
					array_push( $returnNotConnected, $junction );
				}
			}

			return $returnNotConnected;
		}

		return false;
	}


}

?>