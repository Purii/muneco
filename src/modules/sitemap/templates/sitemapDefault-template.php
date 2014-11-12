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
echo $set_nodes_post;
echo "\n";
echo $set_notconnected_post;
echo "\n";
echo $set_nodes_page;
echo "\n";
echo $set_notconnected_page;
echo '</urlset>';