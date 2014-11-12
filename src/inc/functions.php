<?php
namespace MuNeCo;
	/**
	 * General functions which belong to none Class
	 *
	 * @author    Patrick Puritscher
	 * @license   GPL-2.0+
	 * @link      -
	 * @copyright 2014 Patrick Puritscher
	 */
/**
 * @param int $blogID
 * @param int $listelm
 *
 * @return bool|int
 */
function count_post_anchestors( $blogID = null, $listelm = null ) {
	if ( null === $listelm ) {
		return false;
	}
	$counter = 0;
	if ( null != $blogID ) {
		switch_to_blog( $blogID );
	}
	foreach ( get_post_ancestors( $listelm ) as $listelm_parent ) {
		$counter ++;
	}
	if ( null != $blogID ) {
		restore_current_blog();
	}

	return $counter;
}

/**
 * @param $posts       Array of WP_Post Objects
 * @param $searchvalue String
 *
 * @return array
 */
function search_by_title( $posts, $searchvalue ) {
	$response = array();
	foreach ( $posts as $post ) {
		if ( false !== strpos( $post->post_title, $searchvalue ) ) {
			array_push( $response, $post );
		}
	}

	return $response;
}

?>