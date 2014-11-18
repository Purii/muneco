<?php
/**
 * Template Metatags
 *
 * @package   MuNeCo
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
?>
<?php
foreach ( $connections_junctions as $connectedJunction ) {
	echo '<link rel="alternate" href="' . $connectedJunction->permalink . '" hreflang="' . strtolower( $connectedJunction->languagecode ) . '" type="text/html">';
	echo "\n";
}
?>