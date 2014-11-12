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
namespace MuNeCo\coremodules\Networkadmin;

use MuNeCo\Model\Modulehandler;
use MuNeCo\Model\Network;
use MuNeCo\Model\Site;

/**
 * Class ViewController
 * @package MuNeCo\RootModules\Networkadmin
 */
class ViewController {

	/**
	 * Register all Models
	 * Forward updates
	 */
	public function __construct() {
		$moduleHandler = Modulehandler::get_instance();

		require_once( MUNECO_INCSPATH . 'functions.php' );

		/* Check if something has been submitted */
		if ( ! empty( $_POST ) && isset( $_POST["_wpnonce"] ) && isset( $_POST['option_page'] ) ) {
			$settingsGroup_cleaned = 'save_' . str_replace( "muneco_", "", strtolower( $_POST['option_page'] ) );
			if ( method_exists( $this, $settingsGroup_cleaned ) ) {
				call_user_func( array( $this, $settingsGroup_cleaned ) );
			}
		} /* Check if moduleaaction is triggered */
		else if ( isset( $_POST['action'] ) && ( $_POST['action'] == 'activate' || $_POST['action'] == 'deactivate' ) && isset( $_POST['module'] ) && ! empty( $_POST['module'] ) ) {
			if ( $_POST['action'] == 'activate' ) {
				$activation = $moduleHandler->activate_module( $_POST['module'] );
			} else {
				$activation = $moduleHandler->deactivate_Module( $_POST['module'] );
			}
			if ( is_wp_error( $activation ) ) {
				wp_die( $activation, '', array( 'back_link' => true ) );
			}
			/* Reload */
			header( 'Location: ' . $_SERVER['PHP_SELF'] . '?page=muneco_settings&message=1' );
			die;

		}


		add_action( 'network_admin_menu', array( $this, 'register_settings_page' ) );
	}

	/**
	 * save_settings_page
	 */
	private function save_settingspage() {
		if ( wp_verify_nonce( $_POST["_wpnonce"], 'muneco_settingspage' ) ) {
			/* Save langcodes */
			/* Save status */
			foreach ( unserialize( base64_decode( $_POST['siteIDs'] ) ) as $siteID ) {
				$singleSite = new Site( $siteID );
				if ( ! isset( $_POST[ 'muneco_languagecode_' . $siteID ] ) || empty( $_POST[ 'muneco_languagecode_' . $siteID ] ) ) {
					$singleSite->deleteLanguagecode();
				}
				$singleSite->setLanguagecode( $_POST[ 'muneco_languagecode_' . $siteID ] );

				if ( ! isset( $_POST[ 'muneco_status_' . $siteID ] ) ) {
					$singleSite->deleteMunecostatus();
				} else {
					$singleSite->setMunecostatus( $_POST[ 'muneco_status_' . $siteID ] );
				}
			}
			/* Trigger caching */
			$network = new Network();
			$network->getMunecoEnabledSites();
			/* Reload */
			header( 'Location: ' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&message=1' );
			die;
		}
	}


	/******************** FRONTEND *********************/

	/**
	 * Create Menu Page
	 */
	public function register_settings_page() {
		add_menu_page( 'muneco', 'muneco', 'administrator', 'muneco_settings', array(
			&$this,
			'printPage'
		), 'dashicons-admin-links' );
		do_action( 'muneco_networkadmin_addsubpage' );
	}

	/**
	 * Enqueue styles and scripts
	 */
	public function register_admin_scripts() {
		wp_enqueue_style( 'muneco.styles', MUNECO_BASEURL . 'admin/assets/css/muneco-styles.css' );
		wp_enqueue_style( 'muneco.elements', MUNECO_BASEURL . 'admin/assets/css/muneco-elements.css' );


	}


	/**
	 * @return string
	 */
	private function render_templateModule() {
		$moduleHandler    = new Modulehandler();
		$availableModules = $moduleHandler->get_availableModules();
		unset( $moduleHandler );
		ob_start();
		include( 'templates/modules-template.php' );

		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	private function render_templateSites() {
		$networkModel       = new Network();
		$allSites           = $networkModel->getAllSites();
		$isSubdomaininstall = is_subdomain_install();
		unset( $networkModel );
		ob_start();
		include( 'templates/sites-template.php' );

		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function render_templateMessage() {
		ob_start();
		include( 'templates/message-template.php' );

		return ob_get_clean();
	}

	/**
	 * Print the page
	 */
	public function printPage() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		if ( isset( $_GET['message'] ) && ! empty( $_GET['message'] ) ) {
			$self = $this;
			add_action(
				'network_admin_notices', function () use ( &$self ) {
					echo $self->render_templateMessage();
				}
			);
		}
		echo "<div class=\"wrap\">";
		echo $this->render_templateSites();
		echo "<hr>";
		echo $this->render_templateModule();
		echo "</div>";
	}
}

?>