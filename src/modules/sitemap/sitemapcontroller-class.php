<?php
/**
 * Module Name: Sitemap
 * Author: Patrick Puritscher
 * Version: 0.1
 * Description: This Module is still under development. Already refactored!
 * Pro: false
 * Copyright: 2014 Patrick Puritscher
 */
/* Presenter */
namespace MuNeCo\modules\sitemap;


use MuNeCo\modules\IFpresenter;

/**
 * Class sitemap
 * @package MuNeCo\modules\sitemap
 */
class SitemapController implements IFpresenter {
	/**
	 * Called at deactivation
	 * @return bool
	 */
	public static function uninstall() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();

		return true;
	}

	/**
	 * Called at activation
	 * Add sitemap.xml as feed
	 * @return bool
	 */
	public static function install() {
		global $wp_rewrite;
		/**
		 * Only visible if Blog is root
		 */
		add_filter(
			'root_rewrite_rules', function ( $wp_rewrite ) {
				/* Register Sitemap as Feed */
				$feed_rules = array(
					'.*sitemap.xml$' => 'index.php?feed=sitemap'
				);
				$wp_rewrite = $wp_rewrite + $feed_rules;

				return $wp_rewrite;
			}
		);
		$wp_rewrite->flush_rules();

		return true;
	}



	function __construct() {
		/* Runs after wp_loaded or current_screen */
		add_action(
			'do_robotstxt', array( $this, 'add_to_robotstxt' )
		);
		add_action(
			'do_feed_sitemap', function () use ( &$data ) {
				$viewsitemap = new ViewSitemap( $data );
				echo $viewsitemap->render_xmlsitemap();
			}
		);
		/**
		 * Load Networkadmin Subpage
		 */
		add_action( 'muneco_networkadmin_addsubpage', array( $this, 'register_submenu_page' ) );
	}


	/**
	 * Add link to the sitemap.xml
	 */
	public function add_to_robotstxt() {
		echo "Sitemap: " . get_option( 'siteurl' ) . "/sitemap.xml\n";
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
	 * Load the correct VIEW and append the data
	 */
	public function printPage() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		echo '<h2>' . __( 'Sitemap', 'muneco' ) . ' </h2>';
		echo $this->render_templateSettingspage();
	}

	public function register_admin_scripts() {
		wp_enqueue_style( 'muneco.styles', MUNECO_BASEURL . 'admin/assets/css/muneco-styles.css' );
	}

	/**
	 * @return string
	 */
	private function render_templateSettingspage() {
		ob_start();
		include( 'templates/settingspage-template.php' );
		return ob_get_clean();
	}


}