<?php
/**
 *    Interface for additional Modules
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
namespace MuNeCo\modules;

/**
 * Interface IFpresenter
 * @package MuNeCo\modules
 */
interface IFpresenter {
	/**
	 * Called at deactivation
	 * @return boolean
	 */
	public static function uninstall();

	/**
	 * Called at activation
	 * @return boolean
	 */
	public static function install();
}