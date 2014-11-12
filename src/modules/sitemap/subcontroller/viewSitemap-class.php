<?php
/**
 * Module: Sitemap
 * Part: Subcontroller\ViewSitemap
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\modules\sitemap;

use MuNeCo\Model\Network;
use MuNeCo\Model\Site;

/**
 * Class View
 * @package MuNeCo\modules\sitemap
 */
class ViewSitemap {

	/**
	 * @return string
	 */
	public function render_xmlsitemap() {
		$set_nodes_post        = $this->get_NodesByPosttype( 'post' );
		$set_notconnected_post = $this->get_notConnectedByPosttype( 'post' );
		$set_nodes_page        = $this->get_NodesByPosttype( 'page' );
		$set_notconnected_page = $this->get_notConnectedByPosttype( 'page' );
		ob_start();
		include( '../templates/sitemapDefault-template.php' );

		return ob_get_clean();
	}

	/**
	 *
	 * @param string $posttype
	 *
	 * @return string
	 */
	private function get_NodesByPosttype( $post_type ) {
		$xml_set       = '';
		$singleNetwork = new Network();
		foreach (
			$singleNetwork->getAllNodes( $post_type, array(
				'public'  => true,
				'deleted' => false
			) ) as $connectionBlock
		) {
			$xml_set .= $this->generate_single_set( $connectionBlock );
		}

		return $xml_set;
	}


	/**
	 *
	 * @param string $post_type
	 *
	 * @return string
	 */
	private function get_notConnectedByPosttype( $post_type ) {
		extract( $this->vars );
		$xml_all_postspages = '';
		$singleNetwork      = new Network();

		foreach (
			$singleNetwork->getMunecoEnabledSites( false, null, array(
				'public'  => true,
				'deleted' => false
			) ) as $site
		) {
			$singleSite   = new Site( $site['blog_id'] );
			$notConnected = $singleSite->getJunctions( true, $post_type );

			if ( false == $notConnected ) {
				continue;
			}
			foreach ( $notConnected as $notConnectedPP ) {
				if ( isset( $notConnectedPP->connectedWith ) ) {
					continue;
				}
				$xml_all_postspages .= '<url>';
				$xml_all_postspages .= '<loc>' . get_permalink( $notConnectedPP->ID ) . '</loc>';
				$xml_all_postspages .= '</url>';
			}
		}

		return $xml_all_postspages;

	}

	/**
	 * @param $connectionBlock
	 *
	 * @return string
	 */
	private function generate_single_set( $connectionBlock ) {
		$xml_single_set = '';

		foreach ( $connectionBlock as $connectionBID => $connectionPID ) {
			$xml_single_set .= $this->generate_single_block( $connectionBlock, $connectionBID );
		}

		return $xml_single_set;
	}


	/**
	 * @param $connectionBlock
	 * @param $rootBlogID
	 *
	 * @return string
	 */
	private function generate_single_block( $connectionBlock, $rootBlogID ) {
		extract( $this->vars );
		$xml_single_block = '';
		$xml_single_block .= '<url>';
		$connectionPID = $connectionBlock[ $rootBlogID ];
		$post          = $connectionModel->get_PostPage( $rootBlogID, $connectionPID );
		$xml_single_block .= '<loc>' . $post->permalink . '</loc>';

		if ( count( $connectionBlock ) > 1 ) {
			foreach ( $connectionBlock as $connectionBID => $connectionPID ) {
				//$connectionsBlock contains just the IDs!
				$post = $connectionModel->get_PostPage( $connectionBID, $connectionPID );
				$xml_single_block .= '<xhtml:link rel="alternate" hreflang="' . $post->langCode . '" href="' . $post->permalink . '" />';
			}
		}
		$xml_single_block .= '</url>';

		return $xml_single_block;
	}
}