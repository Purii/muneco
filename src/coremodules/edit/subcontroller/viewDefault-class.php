<?php
/**
 * View for Post-/Page-Editing
 * Manipulate Publishbox
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
use MuNeCo\Model\Site;

/**
 * Class PostpageView
 * @package MuNeCo\rootModules\edit
 */
class ViewDefault {


	/**
	 * Construct
	 *
	 */
	function __construct() {
		$currentSite = new Site( get_current_blog_id() );
		if ( $currentSite->getMunecostatus() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
			add_action( 'post_submitbox_misc_actions', array( $this, 'printPage' ) );
		}
	}


	/**
	 * @return string
	 */
	private function render_publishbox() {
		$blogID          = get_current_blog_id();
		$postID          = get_the_ID();
		$currentJunction = new Junction( $blogID, $postID );
		$singleNetwork   = new Network();
		$enabledSites    = $singleNetwork->getMunecoEnabledSites( true );

		$connections_junctions = $currentJunction->getConnections_Junctions( true );
		unset( $currentJunction );
		unset( $singleNetwork );
		ob_start();
		include( MUNECO_COREMODULESPATH . '/edit/templates/publishbox-template.php' );

		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	private function render_thickbox() {

		$blogID          = get_current_blog_id();
		$postID          = get_the_ID();
		$currentJunction = new Junction( $blogID, $postID );
		$singleNetwork   = new Network();

		$connections_junctions = $currentJunction->getConnections_Junctions( true );
		$enabledSites          = $singleNetwork->getMunecoEnabledSites( true );

		$allPosts = array();
		foreach ( $enabledSites as $site ) {
			$thissite                   = new Site( $site->blog_id );
			$allPosts[ $site->blog_id ] = $thissite->getJunctions( true, 'post' );
		}
		unset( $currentJunction );
		unset( $singleNetwork );


		add_thickbox();
		ob_start();
		include( MUNECO_COREMODULESPATH . '/edit/templates/thickbox-template.php' );

		return ob_get_clean();
	}


	public function register_admin_scripts() {
		wp_enqueue_style( 'muneco.styles', MUNECO_BASEURL . 'admin/assets/css/muneco-styles.css' );
		wp_enqueue_style( 'muneco.elements', MUNECO_BASEURL . 'admin/assets/css/muneco-elements.css' );
		wp_enqueue_script( 'muneco.edit-screen', MUNECO_BASEURL . 'admin/assets/js/muneco-editscreen.js' );
		wp_enqueue_script( 'muneco.thickbox', MUNECO_BASEURL . 'admin/assets/js/muneco-thickbox.js' );
	}


	public function printPage() {
		echo $this->render_thickbox();
		echo $this->render_publishbox();
	}

}

?>