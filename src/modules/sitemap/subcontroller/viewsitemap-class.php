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
		$xml_all_notconnectedPosts = $this->get_notconnectedByPosttype( 'post' );
		$xml_all_notconnectedPages = $this->get_notconnectedByPosttype( 'page' );
		$xml_all_blocksPosts = $this->get_nodeBlocks('post');
		$xml_all_blocksPages = $this->get_nodeBlocks('page');
		ob_start();
		include( MUNECO_MODULESPATH . 'sitemap/templates/sitemapdefault-template.php' );
		return ob_get_clean();
	}

	private function get_notconnectedByPosttype( $posttype ) {
		$xml_all_notconnected = '';
		$singleNetwork = new Network();
		foreach($singleNetwork->getMunecoEnabledSites() as $singleSite ) {
			switch_to_blog( $singleSite->blog_id );
			$singleSiteObject = new Site( $singleSite->blog_id);
			$siteJunctions = $singleSiteObject->getJunctions( true, $posttype );
			foreach( $siteJunctions as $siteJunction ) {
				$xml_all_notconnected .= '<url>';
				$xml_all_notconnected .= "\n<loc>" . get_permalink( $siteJunction->ID ) . "</loc>\n";
				$xml_all_notconnected .= "</url>\n";
			}
			restore_current_blog();
		}
		return $xml_all_notconnected;
	}

	private function get_nodeBlocks( $posttype ) {
		$xml_all_blocks = '';
		$singleNetwork = new Network();
		foreach( $singleNetwork->getAllNodes( $posttype ) as $singleNode ) {
			$xml_all_blocks = "<url>";
			foreach( $singleNode  as $partJunction ) {
				$xml_all_blocks .= "\n<loc>" . $partJunction->permalink . "</loc>\n";
				foreach( $singleNode  as $partJunctionSecondlevel ) {
					$xml_all_blocks .= '<xhtml:link rel="alternate" hreflang="' . $partJunctionSecondlevel->languagecode . '" href="' . $partJunctionSecondlevel->permalink . '" />';
				}
			}
			$xml_all_blocks .= "\n</url>\n";
		}
		return $xml_all_blocks;
	}
}