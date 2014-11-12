<?php
namespace MuNeCo;

use MuNeCo\Model\Site;

/**
 * Ajaxfunctions
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
class ajaxHandler {
	/**
	 * @var ajaxHandler
	 */
	private static $instance = null;

	/**
	 * @var \MuNeCo\Model\Sites
	 */
	private $sitesModel;
	/**
	 * @var \MuNeCo\Model\Site
	 */
	private $siteModel;
	/**
	 * @var \MuNeCo\Model\Connections
	 */
	private $connectionsModel;
	/**
	 * @var \MuNeCo\Model\Connection
	 */
	private $connectionModel;


	/**
	 * Singleton - Pattern
	 * @return MuNeCo
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {

		require_once( MUNECO_INCSPATH . 'functions.php' );

		add_action( 'wp_ajax_muneco_linkajax', array( $this, 'linkajax' ) );
	}

	private function responseformat( $response ) {
		return json_encode( $response );
	}

	public function linkajax() {

		check_ajax_referer( 'muneco-ajax', 'ajax_nonce' );

		if ( ! isset( $_POST['searchvalue'] ) || ! isset( $_POST['siteid'] ) || ! isset( $_POST['post_type'] ) ) {
			$response['success'] = false;
			$response['message'] = 'invalidrequest';

			return $this->responseformat( $response );
		}

		if ( ! post_type_exists( $_POST['post_type'] ) ) {
			$response['success'] = false;
			$response['message'] = 'invalidrequest';

			return $this->responseformat( $response );
		}

		$siteid      = intval( $_POST['siteid'] );
		$searchvalue = sanitize_text_field( $_POST['searchvalue'] );
		$posttype    = sanitize_text_field( $_POST['post_type'] );

		$singleSite = new Site( $siteid );
		$result     = $singleSite->getJunctions( true, $posttype );

		$search_result = search_by_title( $result, $searchvalue );

		$response['success'] = true;
		$response['data']    = $search_result;

		echo $this->responseformat( $response );
		die();
	}
}
