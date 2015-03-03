<?php
/**
 * Bind Models an calls View
 *
 * @package   MuNeCo
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\coremodules\edit;

use MuNeCo\Model\Junction;
use MuNeCo\Model\Network;
use MuNeCo\Model\Node;
use MuNeCo\Model\Site;

/**
 * Class ViewController
 * @package MuNeCo\RootModules\Edit
 */
class ViewController {

	/**
	 * Register all Models
	 * Forward updates
	 */
	public function __construct() {
		/* Runs after wp_loaded or current_screen */

		require_once( MUNECO_INCSPATH . 'functions.php' );

		add_action( 'post_updated', array( $this, 'set_connections' ) );

		add_action( 'delete_post', array( $this, 'unset_connections' ) );
		$this->loadSubcontroller();
	}

	/**
	 * Unsets connections at POST_delete
	 * Still there when moved to trash, this is handled in edit/viewcontroller/subcontroller
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function unset_connections( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
		$currentBlogID  = get_current_blog_id();
		$currentSite    = new Site( $currentBlogID );
		$currentNetwork = new Network();

		if ( ! $currentSite->getMunecostatus() ) {
			return $post_id;
		}
		/* Check if more than one site is has enabled muneco */
		if ( count( $currentNetwork->getMunecoEnabledSites( true ) ) <= 1 ) {
			return $post_id;
		}
		if ( ! isset( $_POST['post_connections_nonce'] ) ) {
			return $post_id;
		}
		$nonce = $_POST['post_connections_nonce'];
		if ( ! wp_verify_nonce( $nonce, MUNECO_BASENAME ) ) {
			return $post_id;
		}
		$connections = array();
		foreach ( $currentNetwork->getMunecoEnabledSites( true, array( $currentBlogID ) ) as $site ) {
			if ( isset( $_POST[ 'post-connection-' . $site->blog_id ] ) ) {
				$connections[ $site->blog_id ] = 0;
			}
		}
		$currentJunction = new Junction( $currentBlogID, $post_id );
		$currentJunction->setConnections( $connections );

		return true;
	}
	/**
	 * Sets connections at POST_update
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function set_connections( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
		$currentBlogID  = get_current_blog_id();
		$currentSite    = new Site( $currentBlogID );
		$currentNetwork = new Network();

		if ( ! $currentSite->getMunecostatus() ) {
			return $post_id;
		}
		/* Check if more than one site is has enabled muneco */
		if ( count( $currentNetwork->getMunecoEnabledSites( true ) ) <= 1 ) {
			return $post_id;
		}

		if ( ! isset( $_POST['post_connections_nonce'] ) ) {
			return $post_id;
		}
		$nonce = $_POST['post_connections_nonce'];
		if ( ! wp_verify_nonce( $nonce, MUNECO_BASENAME ) ) {
			return $post_id;
		}

		$connections = array();
		foreach ( $currentNetwork->getMunecoEnabledSites( true, array( $currentBlogID ) ) as $site ) {
			if ( isset( $_POST[ 'post-connection-' . $site->blog_id ] ) ) {
				$connections[ $site->blog_id ] = sanitize_text_field( $_POST[ 'post-connection-' . $site->blog_id ] );
			}
		}
		$currentJunction = new Junction( $currentBlogID, $post_id );
		$currentJunction->setConnections( $connections );

		return true;
	}

	/**
	 * Load the correct VIEW and append the data
	 */
	private function loadSubcontroller() {

		/* Add later a metabox-view *
		if ( get_current_screen() === null ) {
			$screenbase = null;
		} else {
			$screenbase = \get_current_screen()->base;
		}

		$data = $this->get_data();
		switch ( $screenbase ) {
			case "post" :
				new PostpageView( $data );
				break;
			case "page" :
				new PostpageView( $data );
				break;
			default :
				new DefaultView( $data );
		}
		*/

		$currentSubcontroller = new ViewDefault();
	}
}

?>