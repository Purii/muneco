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
namespace MuNeCo\coremodules\frontend;

use MuNeCo\Model\Junction;
use MuNeCo\Model\Site;

/**
 * Class ViewController
 * @package MuNeCo\RootModules\Frontend
 */
class ViewController {

	/**
	 * Register all Models
	 * Forward updates
	 */
	public function __construct() {
		/* Runs after wp_loaded or current_screen */
		require_once( MUNECO_INCSPATH . 'functions.php' );

		add_filter( 'language_attributes', array( &$this, 'set_wp_language_attribute' ) );
		add_action( 'wp_head', array( &$this, 'render_templateMetatags' ) );

		do_action( 'muneco_frontend_init' );
	}

	/**
	 * @return bool
	 */
	public function render_templateMetatags() {
		$currentPostID         = get_the_ID();
		$currentBlogID         = get_current_blog_id();
		$currentSite           = new Site( $currentBlogID );
		$currentJunction       = new Junction( $currentBlogID, $currentPostID );
		$connections_junctions = $currentJunction->getConnections_Junctions();
		/* Only run if singlepost and has connections and is enabled */
		if (
			! $currentSite->getMunecostatus()
			|| ! is_singular()
			|| get_the_ID() === null
			|| $connections_junctions < 1
		) {
			return true;
		}

		unset( $currentSite );
		unset( $currentJunction );
		ob_start();
		require( 'templates/metatags-template.php' );
		echo ob_get_clean();

		return true;
	}

	/**
	 * @param $output
	 *
	 * @return string
	 */
	public function set_wp_language_attribute( $output ) {
		$currentBlogID = get_current_blog_id();
		$currentSite   = new Site( $currentBlogID );
		/* Only overwrite if muneco is active */
		if ( $currentSite->getMunecostatus() != 1 ) {
			return $output;
		}

		return str_replace( get_bloginfo( 'language' ), $currentSite->getLanguagecode(), $output );
	}
}

?>