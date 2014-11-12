<?php
/**
 * Module: Sitemap
 * Part: Subcontroller\ViewNetworkadmin
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\modules\sitemap;

/**
 * Class View
 * @package MuNeCo\modules\sitemap
 */
class ViewNetworkadmin {

	/**
	 * Construct
	 *
	 * @param array $vars
	 */
	function __construct() {
		add_action( 'muneco_networkadmin_addsubpage', array( $this, 'register_submenu_page' ) );
	}


	/**
	 * Create Menu Page
	 */
	public function register_submenu_page() {
		add_submenu_page( 'muneco_settings', __( 'Sitemap', 'muneco' ), __( 'Sitemap', 'muneco' ), 'administrator', 'muneco_sitemap', array(
			$this,
			'printPage'
		) );
	}


	/**
	 * @return string
	 */
	private function render_templateMessage() {
		ob_start();
		include( '../templates/message-template.php' );

		return ob_get_clean();
	}

	public function register_admin_scripts() {
		wp_enqueue_style( 'muneco.styles', MUNECO_BASEURL . 'admin/assets/css/muneco-styles.css' );
	}

	/**
	 * @return bool
	 */
	public function printPage() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		echo '<h2>' . __( 'Sitemap', 'muneco' ) . '</h2>';
		echo $this->render_templateMessage();

		return true;
	}


}