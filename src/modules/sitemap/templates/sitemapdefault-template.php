<?php
/**
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
header( 'Content-Type: text/xml;charset=' . get_option( 'blog_charset' ), true );
echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?>';
echo "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:xhtml="http://www.w3.org/1999/xhtml">';
echo "\n";
echo $xml_all_blocksPosts;
echo "\n";
echo $xml_all_blocksPages;
echo "\n";
echo $xml_all_notconnectedPosts;
echo "\n";
echo $xml_all_notconnectedPages;
echo '</urlset>';