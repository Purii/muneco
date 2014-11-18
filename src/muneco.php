<?php

/**
 * Plugin Name: MuNeCo
 * Plugin URI: http://purii.github.io/MuNeCo
 * Author: Patrick Puritscher
 * Description: Add multilanguage support for networks.
 * Version: 0.2.1
 * Network: true
 * Text Domain: MuNeCo
 * Requirements: PHP5.3 (Namespace) & Anonyme Funktionen
 *
 */
?>
<?php
/*
 * 
 * Main Controller
 * 
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
require( 'inc/constants.php' );
load_plugin_textdomain( 'muneco', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/**
 * Class MuNeCo
 */
class MuNeCo {

	/**
	 * @var array
	 */
	private $currentScreen = array();

	/**
	 * @var MuNeCo
	 */
	private static $instance = null;
	/**
	 * @var bool
	 */
	private $initialized = false;

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
	public function __construct() {
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		/*
		 * Hooks to identify the current screen
		 */
		add_action( 'current_screen', array( &$this, 'init_muneco' ) );
		add_action( 'wp_loaded', array( &$this, 'init_muneco' ) );
		spl_autoload_register( array( &$this, 'autoload' ) );


		/**
		 * Ajax-Handler
		 */
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			require( MUNECO_INCSPATH . 'ajax.php' );
			MuNeCo\ajaxHandler::get_instance();
		}
	}

	/**
	 * Main init
	 * Load class depending on current screen
	 *
	 * @param object $screen
	 *
	 * @return bool
	 */
	public function init_muneco( $screen ) {
		/*
		 * Check whether the screen is recognized and needed
		 */
		if ( $this->initialized || ! $this->setCurrentscreen( $screen ) ) {
			return false;
		}
		$this->initialized = true;
		if ( $this->currentScreen['edit'] ) {
			new \MuNeCo\coremodules\Edit\ViewController();
		} else if ( 'networkadmin' == $this->currentScreen['type'] ) {
			new \MuNeCo\coremodules\Networkadmin\ViewController();
		} else if ( 'frontend' == $this->currentScreen['type'] ) {
			new \MuNeCo\coremodules\Frontend\ViewController();
		}

		$moduleModel = \MuNeCo\Model\Modulehandler::get_instance();
		$moduleModel->load_activated();

		return true;
	}

	/**
	 * @param string $classwn
	 *
	 * @return bool
	 */
	private function autoload( $classwn ) {
		// Class With Namespace
		$classwn    = strtolower( $classwn );
		$namespaces = explode( "\\", $classwn );

		if ( 'muneco' != $namespaces[0] ) {
			return false;
		}
		//print_r($classwn);
		$class = end( $namespaces );
		if ( 'model' == $namespaces[1] ) {
			$file = MUNECO_MODELPATH . $class . '-class.php';
			if ( file_exists( $file ) ) {
				require_once( $file );

				return true;
			}
		} else if ( 'coremodules' == $namespaces[1] ) {
			$file = MUNECO_COREMODULESPATH . $namespaces[2] . '/' . $class . '-class.php';
			if ( file_exists( $file ) ) {
				require_once( $file );

				return true;
			} else {
				$fileSubcontroller = MUNECO_COREMODULESPATH . $namespaces[2] . '/subcontroller/' . $class . '-class.php';
				if ( file_exists( $fileSubcontroller ) ) {
					require_once( $fileSubcontroller );

					return true;
				}
			}
		} else if ( 'modules' == $namespaces[1] ) {
			if ( 'ifpresenter' == $namespaces[2] ) {
				$file = MUNECO_MODULESPATH . $class . '-class-interface.php';
				if ( file_exists( $file ) ) {
					require_once( $file );

					return true;
				}
			}
			$file = MUNECO_MODULESPATH . $namespaces[2] . '/' . $class . '-class.php';
			if ( file_exists( $file ) ) {
				require_once( $file );

				return true;
			} else {
				$fileSubcontroller = MUNECO_MODULESPATH . $namespaces[2] . '/subcontroller/' . $class . '-class.php';
				if ( file_exists( $fileSubcontroller ) ) {
					require_once( $fileSubcontroller );

					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Identify the current screen
	 *
	 * @param object $screen
	 *
	 * @return bool
	 */
	private function setCurrentscreen( $screen = null ) {
		/* Frontend */
		if ( ! is_admin() ) {
			$this->currentScreen['edit'] = false;
			$this->currentScreen['type'] = 'frontend';

			return true;
		} /* Networkadmin */
		else if ( is_network_admin() ) {
			$this->currentScreen['edit'] = false;
			$this->currentScreen['type'] = 'networkadmin';

			return true;

		} /* Backend */
		else if ( $screen != null && 'post' == $screen->base ) {
			if ( 'page' == $screen->post_type ) {
				$this->currentScreen['edit'] = true;
				$this->currentScreen['type'] = 'page';

				return true;
			} else if ( 'post' == $screen->post_type ) {
				$this->currentScreen['edit'] = true;
				$this->currentScreen['type'] = 'post';

				return true;
			}
		}

		return false;
	}

	/**
	 * Register Settings
	 * Usable by the WordPress Settingspage
	 */
	public function register_settings() {
		register_setting( 'muneco_settingspage', 'muneco_languagecode' );
		register_setting( 'muneco_settingspage', 'muneco_status' );
	}

}

/*
 * Load Plugin
 */
require( 'inc/requirements-class.php' );
$requirements = new MuNeCo_requirements();
if ( ! $requirements->check() ) {
	$requirements->display_admin_notice();
} else {
	add_action( 'plugins_loaded', array( 'MuNeCo', 'get_instance' ) );
}

/*
 * (De-)Activation
 */
function perform_activation() {
	if ( ! defined( 'MUNECO_INSTALLATION' ) ) {
		define( 'MUNECO_INSTALLATION', true );
	}
	require_once( 'inc/upgradeinstall-class.php' );
	$upgradeinstall = new MuNeCo_UpgradeInstall();
	$upgradeinstall->action();
}

register_activation_hook( __FILE__, 'perform_activation' );


/*
 * Examples
 */
//add_action('MuNeCo_networkadmin_after', array( 'MuNeCo_Sitemap', 'get_instance' ) );

/* Api Call 
 * MuNeCo::get_instance()->api( 'getCurrentPath', array('Patrick', 'hallo' ) );
 */
?>
