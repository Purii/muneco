<?php
/**
 * Handles Module operations
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\Model;

/**
 * Class Module
 * @package MuNeCo\Model
 * @since   1.0.0
 */
final class Modulehandler {
	/**
	 * @var Modulehandler
	 */
	private static $instance;

	/**
	 * Singleton - Pattern
	 * @return Modulehandler
	 * @since 1.0.0
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Activates Module
	 *
	 * @param string $moduleName
	 *
	 * @return bool|\WP_Error
	 * @since 1.0.0
	 */
	public function activate_module( $moduleName ) {
		/* User logged in? */
		auth_redirect();
		/* User can do? */
		if ( ! current_user_can( 'manage_network_plugins' ) ) {
			wp_die( __( 'You do not have sufficient permissions to manage MuNeCo-modules for this site.', 'MuNeCo' ) );
		}
		/* Plugin available? */
		if ( ! file_exists( MUNECO_MODULESPATH . $moduleName . '/' . $moduleName . 'controller-class.php' ) ) {
			return new \WP_Error( 'muneco_moduleActivation', __( "The Module is not available", 'MuNeCo' ) . '.' );
		}
		/* Plugin already activated? */
		if ( in_array( $moduleName, $this->get_activatedModules() ) ) {
			return new \WP_Error( 'muneco_moduleActivation', __( 'The Module is already active', 'MuNeCo' ) . '.' );
		}
		/* Plugin install */
		require MUNECO_MODULESPATH . $moduleName . '/' . $moduleName . 'controller-class.php';
		$installmodule = "\\MuNeCo\\modules\\$moduleName\\$moduleName" . "Controller";
		if ( ! method_exists( $installmodule, 'install' ) && ! method_exists( $installmodule, 'uninstall' ) ) {
			return new \WP_Error( 'muneco_moduleActivation', __( "The Module is not initialized correctly", 'MuNeCo' ) . '.' );
		}
		if ( ! $installmodule::install() ) {
			return new \WP_Error( 'muneco_moduleActivation', __( "The Module cannot be installed", 'MuNeCo' ) . '.' );
		}
		$activatedModules = $this->get_activatedModules();
		array_push( $activatedModules, $moduleName );
		update_site_option( 'muneco_activatedModules', $activatedModules );

		return true;
	}

	/**
	 * Deactivates Module
	 *
	 * @param string $moduleName
	 *
	 * @return bool|\WP_Error
	 * @since 1.0.0
	 */
	public function deactivate_module( $moduleName ) {
		/* User logged in? */
		auth_redirect();
		/* User can do? */
		if ( ! current_user_can( 'manage_network_plugins' ) ) {
			wp_die( __( 'You do not have sufficient permissions to manage MuNeCo-modules for this site.' ) );
		}
		/* Plugin uninstall */
		require_once MUNECO_MODULESPATH . $moduleName . '/' . $moduleName . 'controller-class.php';
		$installmodule = "\\MuNeCo\\modules\\$moduleName\\$moduleName" . "Controller";
		if ( ! method_exists( $installmodule, 'uninstall' ) ) {
			return new \WP_Error( 'muneco_moduleActivation', __( "The Module isn't correctly initialized", 'MuNeCo' ) );
		}
		if ( ! $installmodule::uninstall() ) {
			return new \WP_Error( 'muneco_moduleActivation', __( "The Module can't get uninstalled", 'MuNeCo' ) );
		}
		$activatedModules = $this->get_activatedModules();
		$activatedModules = array_diff( $activatedModules, array( $moduleName ) );
		update_site_option( 'muneco_activatedModules', $activatedModules );

		return true;
	}

	/**
	 * Load Module by NEW-operator
	 *
	 * @param string $moduleClass
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function get_moduleInstance( $moduleClass ) {
		if ( file_exists( MUNECO_MODULESPATH . $moduleClass . '/' . $moduleClass . 'controller-class.php' ) ) {
			$classname = "\\MuNeCo\\modules\\$moduleClass\\$moduleClass" . "Controller";

			return new $classname();
		}

		return false;
	}

	/**
	 * @return array|mixed
	 * @since 1.0.0
	 */
	public function get_activatedModules() {
		$activated = get_site_option( 'muneco_activatedModules' );
		if ( false == $activated ) {
			return array();
		}

		return $activated;
	}

	/**
	 * List all Elements from Module-Directory
	 * @return array
	 * @since 1.0.0
	 */
	public function get_availableModules() {
		$activatedModules = $this->get_activatedModules();
		$availableModules = array();
		$path             = realpath( MUNECO_MODULESPATH );
		$objects          = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path ), \RecursiveIteratorIterator::SELF_FIRST );
		foreach ( $objects as $name => $object ) {
			if ( fnmatch( "*controller-class.php", $name ) ) {
				$modulename      = str_replace( "controller-class.php", "", basename( $name ) );
				$default_headers = array(
					'Name'        => 'Module Name',
					'Version'     => 'Version',
					'Description' => 'Description',
					'Pro'         => 'Pro'
				);

				$fileData = get_file_Data( $object, $default_headers );

				$fileData['Pro'] = ( 'true' == strtolower( $fileData['Pro'] ) );
				/* Hide Pro-Modules */
				if ( $fileData['Pro'] ) {
					continue;
				}
				if ( empty( $fileData['Name'] ) ) {
					continue;
				}

				$availableModules[ $modulename ] = array();

				if ( in_array( $modulename, $activatedModules ) ) {
					$availableModules[ $modulename ]['active'] = true;
				} else {
					$availableModules[ $modulename ]['active'] = false;
				}
				$availableModules[ $modulename ] = array_merge( $fileData, $availableModules[ $modulename ] );
			}
		}

		return $availableModules;
	}

	/**
	 * Load active Modules
	 * @since 1.0.0
	 */
	public function load_activated() {
		$activated = $this->get_activatedModules();
		foreach ( $activated as $activeModule ) {
			$this->get_moduleInstance( $activeModule );
		}
	}
} 