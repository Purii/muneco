<?php
/**
 * All Connections
 *
 * @package   MuNeCo
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\Model;
/**
 * Class Connections
 * @package MuNeCo\Model
 * @since   1.0.0
 */
final class Network {

	public function construct() {
		return true;
	}

	/**
	 * @param bool $not_from_cache
	 * @param array $except
	 * @param array $wpargs
	 *
	 * @return array|mixed
	 * @since 1.0.0
	 */
	public function getMunecoEnabledSites( $not_from_cache = false, $except = null, $wpargs = array() ) {
		$enabledSites = array();

		/* Check if there's one in Cache */
		if ( ! $not_from_cache && false != get_site_transient( 'muneco_enabledSites' ) ) {
			$enabledSites = get_site_transient( 'muneco_enabledSites' );
		}

		if ( null != $except && ! is_array( $except ) ) {
			$except = null;
		}

		if ( empty( $enabledSites ) ) {
			foreach ( wp_get_sites( $wpargs ) as $site ) {
				$singleSite = new Site( $site['blog_id'] );
				if ( $singleSite->getMunecostatus() ) {
					array_push( $enabledSites, $singleSite->getBlogMeta() );
				}
			}
		}
		/* Performance Issue */
		set_site_transient( 'muneco_enabledSites', $enabledSites, 5 * MINUTE_IN_SECONDS );

		if ( null != $except ) {
			$enabledSitesFiltered = array();
			foreach ( $enabledSites as $site ) {
				if ( ! in_array( $site->blog_id, $except ) ) {
					array_push( $enabledSitesFiltered, $site );
				}
			}
			$enabledSites = $enabledSitesFiltered;
		}

		return $enabledSites;
	}


	/**
	 * Get all Sites/Blogs with additional meta
	 * @return Object
	 */
	public function getAllSites() {
		$allSites = array();
		foreach ( wp_get_sites() as $site ) {
			$siteModel            = new Site( $site['blog_id'] );
			$site['languagecode'] = $siteModel->getLanguagecode();
			$site['munecostatus'] = $siteModel->getMunecostatus();
			array_push( $allSites, $site );
		}

		return $allSites;
	}

	/**
	 * Build connection-nodes
	 *
	 * @param string $post_type
	 * @param boolean $onlyPIDs if set the result will only contain the PID not the whole junction. This setting needs more memory
	 *
	 * @return array    Index is BID and Value is an Object with PID.
	 * @since 1.0.0
	 */
	public function getAllNodes( $post_type = 'post', $onlyPIDs = false ) {
		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		$network  = new Network();
		$allSites = $network->getMunecoEnabledSites();
		$allNodes = array();

		foreach ( $allSites as $site ) {
			$singleSite = new Site( $site->blog_id );
			$subnodes   = $singleSite->getNodesByBID( $post_type );
			foreach ( $subnodes as $pid => $connections ) {
				foreach ( $connections as $connection ) {
					if (
						array_key_exists( $connection['cbid'], $allNodes )
						&& array_key_exists( $connection['cpid'], $allNodes[ $connection['cbid'] ] )
						&& array_key_exists( $site->blog_id, $allNodes[ $connection['cbid'] ][ $connection['cpid'] ] )
						&& $allNodes[ $connection['cbid'] ][ $connection['cpid'] ][ $site->blog_id ] == $pid
					) {
						/*
						 * Junction is already used
						 */
						continue;
					}
					$allNodes[ $site->blog_id ][ $pid ] = array( $connection['cbid'] => $connection['cpid'] );
				}
			}
		}

		/* Now it is sorted by BID
		 * Build nodes
		 */
		$connectionNodes = array();
		foreach ( $allNodes as $BID => $PIDAndConnections ) {
			foreach ( $PIDAndConnections as $PID => $connections ) {
				$connectionNode = array();

				$connectionNode[ $BID ] = $PID;
				$connectionNode += $connections;

				array_push( $connectionNodes, $connectionNode );
			}
		}

		/*
		 * $connectionNodes array looks like [BID] = PID
		 * To return the same structure every time we create an empty Object if $onlyPIDs is true
		 */
		$connectionNodes_asObject = array();
		foreach ( $connectionNodes as $connectionNode ) {
			$connectionNode_asObject = array();
			foreach ( $connectionNode as $BID => $PID ) {

				if ( $onlyPIDs ) {
					$emptyObject                     = new \stdClass();
					$emptyObject->ID                 = $PID;
					$connectionNode_asObject[ $BID ] = $emptyObject;
					unset( $emptyObject );
				} else {
					$wppostObject                    = new Junction( $BID, $PID );
					$connectionNode_asObject[ $BID ] = $wppostObject->getJunction();
					unset( $wppostObject );
				}
			}
			array_push( $connectionNodes_asObject, $connectionNode_asObject );
		}

		// echo '<pre>';
		//print_r($connectionNodes_asObject);
		//echo '</pre>';
		return $connectionNodes_asObject;
	}
}

?>
