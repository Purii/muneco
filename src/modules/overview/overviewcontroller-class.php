<?php
/**
 * Module Name: Overview
 * Author: Patrick Puritscher
 * Version: 0.1
 * Description: This Module is still under development. Not refactored!
 * Pro: false
 * Copyright: 2014 Patrick Puritscher
 */
/* Presenter */
namespace MuNeCo\modules\overview;

use MuNeCo\Model\Network;
use MuNeCo\Model\Site;
use MuNeCo\modules\IFpresenter;

/**
 * Class overview
 * @package MuNeCo\modules\overview
 */
class OverviewController implements IFpresenter {
	/**
	 * Called at deactivation
	 * @return boolean
	 */
	public static function uninstall() {
		return true;
	}

	/**
	 * Called at activation
	 * @return boolean
	 */
	public static function install() {
		return true;
	}

	function __construct() {
		/* Runs after wp_loaded or current_screen */
		add_action( 'muneco_networkadmin_addsubpage', array( $this, 'register_submenu_page' ) );
	}


	/**
	 * Create Menu Page
	 */
	public function register_submenu_page() {
		add_submenu_page( 'muneco_settings', __( 'Overview', 'muneco' ), __( 'Overview', 'muneco' ), 'administrator', 'muneco_overview', array(
			$this,
			'printPage'
		) );
	}


	/**
	 * @return string
	 */
	private function render_templateNodes() {
		$singleNetwork = new Network();
		$allNodes      = $singleNetwork->getAllNodes();
		$enabledSites  = $singleNetwork->getMunecoEnabledSites( true );
		unset( $singleNetwork );
		ob_start();
		include( 'templates/nodes-template.php' );

		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	private function render_templateNotconnected() {
		ob_start();
		$singleNetwork          = new Network();
		$enabledSites           = $singleNetwork->getMunecoEnabledSites( true );
		$junctions_notconnected = array();
		foreach ( $enabledSites as $site ) {
			$currentSite                              = new Site( $site->blog_id );
			$junctions_notconnected[ $site->blog_id ] = $currentSite->getJunctions( true );
			unset( $currentSite );
		}
		unset( $singleNetwork );
		include( 'templates/notconnected-template.php' );

		return ob_get_clean();
	}


	public function register_admin_scripts() {
		wp_enqueue_style( 'muneco.styles', MUNECO_BASEURL . 'admin/assets/css/muneco-styles.css' );

	}


	/**
	 * Load the correct VIEW and append the data
	 */
	public function printPage() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		echo '<h2>' . __( 'Overview', 'muneco' ) . ' - ' . strtoupper( 'posts' ) . '</h2>';


		echo '<div class="MuNeCo-overview-liquid-left">';
		echo $this->render_templateNodes();
		echo '</div>';

		echo '<div class="MuNeCo-overview-liquid-right">';
		echo $this->render_templateNotconnected();
		echo '</div>';


	}


}