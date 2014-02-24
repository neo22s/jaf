<?php
/**
 * Abstract controller class.
 *
 * @package     JAF
 * @subpackage  Core
 * @category    Controller
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */
abstract class Controller
{
   
	/**
	 * Automatically executed before the controller action
	 */
	public static function before()
	{
		// hook by default
		do_action(get_called_class().'_before');
	}

	/**
	 * Automatically executed after the controller action.
	 */
	public static function after()
	{
		// hook by default
		do_action(get_called_class().'_after');
	}
}