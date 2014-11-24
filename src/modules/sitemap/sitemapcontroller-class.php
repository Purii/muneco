<?php
/**
 * Module Name: Sitemap
 * Author: Patrick Puritscher
 * Version: 0.1
 * Description: This Module is still under development. Not refactored!
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


	/**
	 * Add link to the sitemap.xml
	 */
	public function add_to_robotstxt() {
		echo "Sitemap: " . get_option( 'siteurl' ) . "/sitemap.xml\n";
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
	}


}