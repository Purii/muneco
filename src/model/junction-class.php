<?php
/**
 * Single Connection
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\Model;
/**
 * Class Node
 * A Junction is identified by a containing blogID with postID
 * @package MuNeCo\Model
 * @since   1.0.0
 */
final class Junction {

	/**
	 * @var int
	 */
	private $blogID;

	/**
	 * @var int
	 */
	private $postID;

	public function __construct( $blogID, $postID ) {
		/* Blog existend? */
		if ( ! new Site( $blogID ) ) {
			return false;
		}

		$this->blogID = intval( $blogID );
		$this->postID = intval( $postID );

		/* Node existend? */
		if ( ! $this->getJunction() ) {
			return false;
		}
	}

	/**
	 * Get whole Post/Page/CPT with additional permalink, languagecode & posttype
	 *
	 * @return null|\WP_Post
	 * @since 1.0.0
	 */
	public function getJunction() {
		$singleSite = new Site( $this->blogID );
		switch_to_blog( $this->blogID );
		$the_post = get_post( $this->postID );
		if ( $the_post ) {
			$the_post->permalink    = get_permalink( $this->postID );
			$the_post->posttype     = get_post_type( $this->postID );
			$the_post->languagecode = $singleSite->getLanguagecode();
		}
		restore_current_blog();

		return $the_post;
	}

	/**
	 * Overwrite connections
	 * Triggers Cache-Refresh
	 *
	 * @param int $current_pid
	 * @param array $connectionsArray
	 *
	 * @since 1.0.0
	 */
	public function setConnections( $connectionsArray ) {
		global $wpdb;
		$current_pid = $this->postID;
		$current_bid = $this->blogID;

		/* Connections is an Array from frontend
		 * Key is BlogID
		 * Value is PostID
		 */

		/* Reset Current */
		$current_connections = $this->getConnections( true );
		$wpdb->update( MUNECO_TABLE_CONNECTIONS, array( 'cpid' => 0, 'cbid' => 0 ), array(
			'pid' => $current_pid,
			'bid' => $current_bid
		), '%d', '%d' );
		$wpdb->update( MUNECO_TABLE_CONNECTIONS, array( 'cpid' => 0, 'cbid' => 0 ), array(
			'cpid' => $current_pid,
			'cbid' => $current_bid
		), '%d', '%d' );


		/* concluded relations *
		foreach ( $current_connections as $current_connected_bid => $current_connected_pid ) {
			foreach ( $current_connections as $current_connected_bid_inner => $current_connected_pid_inner ) {
				if ( $current_connected_bid != $current_connected_bid_inner ) {
					$wpdb->update( MUNECO_TABLE_CONNECTIONS, array( 'cpid' => 0, 'cbid' => 0 ), array( 'pid' => $current_connected_pid, 'bid' => $current_connected_bid, 'cpid' => $current_connected_pid_inner, 'cbid' => $current_connected_bid_inner ), '%d', '%d' );
				}
			}
		}

		*/
		/* Reset New */
		foreach ( $connectionsArray as $connected_bid => $connected_pid ) {
			$wpdb->update( MUNECO_TABLE_CONNECTIONS, array( 'cpid' => 0, 'cbid' => 0 ), array(
				'pid' => $connected_pid,
				'bid' => $connected_bid
			), '%d', '%d' );
			$wpdb->update( MUNECO_TABLE_CONNECTIONS, array( 'cpid' => 0, 'cbid' => 0 ), array(
				'cpid' => $connected_pid,
				'cbid' => $connected_bid
			), '%d', '%d' );
		}
		/**** Update or Insert ****/
		foreach ( $connectionsArray as $connection_bid => $connection_pid ) {

			if ( $connection_pid == 0 ) {
				continue;
			}

			$query_1 = $wpdb->prepare( "UPDATE " . MUNECO_TABLE_CONNECTIONS . " SET cpid = %d, cbid = %d WHERE cpid = 0 AND cbid = 0 AND pid = %d AND bid = %d LIMIT 1", $connection_pid, $connection_bid, $current_pid, $current_bid );
			$existed = $wpdb->query( $query_1 );

			$query_2   = $wpdb->prepare( "UPDATE " . MUNECO_TABLE_CONNECTIONS . " SET cpid = %d, cbid = %d WHERE cpid = 0 AND cbid = 0 AND pid = %d AND bid = %d LIMIT 1", $current_pid, $current_bid, $connection_pid, $connection_bid );
			$existed_2 = $wpdb->query( $query_2 );

			if ( $existed == 0 ) {
				$wpdb->insert( MUNECO_TABLE_CONNECTIONS, array(
					'pid'  => $current_pid,
					'bid'  => $current_bid,
					'cpid' => $connection_pid,
					'cbid' => $connection_bid
				), '%d' );
			}
			if ( $existed_2 == 0 ) {
				$wpdb->insert( MUNECO_TABLE_CONNECTIONS, array(
					'pid'  => $connection_pid,
					'bid'  => $connection_bid,
					'cpid' => $current_pid,
					'cbid' => $current_bid
				), '%d' );
			}
			/* concluded relations *
			foreach ( $connectionsArray as $connection_bid_inner => $connection_pid_inner ) {
				if ( $connection_bid == $connection_bid_inner || $connection_pid_inner == 0 ) {
					continue;
				}
				$query_1 = $wpdb->prepare( "UPDATE " . MUNECO_TABLE_CONNECTIONS . " SET cpid = %d, cbid = %d WHERE cpid = 0 AND cbid = 0 AND pid = %d AND bid = %d LIMIT 1", $connection_pid, $connection_bid, $connection_pid_inner, $connection_bid_inner );
				$existed = $wpdb->query( $query_1 );

				if ( $existed == 0 ) {
					// Do Insert
					$wpdb->insert( MUNECO_TABLE_CONNECTIONS, array( 'pid' => $connection_pid_inner, 'bid' => $connection_bid_inner, 'cpid' => $connection_pid, 'cbid' => $connection_bid ), '%d' );
				}

			}
			*/
		}


		/**** Trigger caching ****/
		Transient::get_instance()->clearNodes();
	}

	/**
	 * Get connections
	 * Post type should set automatically -> you cannot connect between post and page - if data is correct!
	 *
	 * @param bool $notFromCache
	 * @param int[] $onlyBIDs if not set all active sites are scanned
	 *
	 * @return array [$BID] = $PID | [$BID] = 0
	 * @since 1.0.0
	 */
	public function getConnections( $notFromCache = false, $onlyBIDs = array() ) {
		$currentBID = $this->blogID;
		$currentPID = $this->postID;

		//Needed? Performance?
		if ( ! $notFromCache && false != get_site_transient( 'muneco_getConnections_BID' . $currentBID . '_PID' . $currentPID ) ) {
			return get_site_transient( 'muneco_getConnections_BID' . $currentBID . '_PID' . $currentPID );
		}

		global $wpdb;
		$connected_1 = $wpdb->get_results(
			"SELECT bid, pid FROM " . MUNECO_TABLE_CONNECTIONS . " WHERE cbid = $currentBID AND cpid = $currentPID AND bid != 0 AND pid != 0",
			"ARRAY_A"
		);
		$connected_2 = $wpdb->get_results(
			"SELECT cbid as bid, cpid as pid FROM " . MUNECO_TABLE_CONNECTIONS . " WHERE bid = $currentBID AND pid = $currentPID AND cbid != 0 AND cpid != 0",
			"ARRAY_A"
		);
		$connected   = array_merge( $connected_1, $connected_2 );
		// Set BIP as index
		$connection_sort = array();
		foreach ( $connected as $connection ) {
			/* Check if Blog is active */
			$singleSite = new Site( $connection['bid'] );
			if ( $singleSite->getMunecostatus() && ( empty( $onlyBIDs ) || in_array( $connection['bid'], $onlyBIDs ) ) ) {
				$connection_sort[ $connection['bid'] ] = intval( $connection['pid'] );
			}
		}

		/* Write in Cache */
		set_site_transient( 'muneco_getConnections_BID' . $currentBID . '_PID' . $currentPID, $connection_sort, 30 * MINUTE_IN_SECONDS );

		return $connection_sort;
	}


	/**
	 * Get connections with whole Junctions
	 * uses Junction::getConnections()
	 *
	 * @param bool $notFromCache
	 * @param array $onlyBIDs if not set all active sites are scanned
	 *
	 * @return array [$BID] = $WP_Post_Object | [$BID] = 0
	 * @since 1.0.0
	 */
	public function getConnections_Junctions( $notFromCache = false, $onlyBIDs = array() ) {
		$connections     = $this->getConnections( $notFromCache, $onlyBIDs );
		$connection_sort = array();
		foreach ( $connections as $connectionBID => $connectionPID ) {
			$singleJunction                    = new Junction( $connectionBID, $connectionPID );
			$connection_sort[ $connectionBID ] = $singleJunction->getJunction();
			unset( $singleJunction );
		}

		return $connection_sort;
	}

}

?>