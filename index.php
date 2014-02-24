<?php
/**
 * All the requests goes to this index.
 *
 * @package     JAF
 * @subpackage  Core
 * @category    Script
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */

//CORE VERSION
define('VERSION','0.1');
//CORE HOOKS,  check Hooks class and Router
define('CORE_HOOKS',TRUE);

/*
 * This defines the paths of the CORE , APP and PUBLIC
 * By default configured so it works at any hosting
 * You can move CORE folder outside the public folder so you can share it with other applications
 * We recommend you this and to move APP (also by renaming the name) so with 1 installation you can have many APPs sharing the core
 */

//MAIN PATH DEFINES
define('BASE_PATH',         __DIR__.'/');
define('CORE_PATH',         BASE_PATH.'core/');
define('VENDOR_PATH',       CORE_PATH.'vendor/');

//YOUR APP PATHS
define('APP_PATH',          BASE_PATH.'app/');
define('PUBLIC_PATH',       BASE_PATH.'');//better to have it in another folder
define('LOCALES_PATH',      APP_PATH.'locales/');
define('CLASSES_PATH',      APP_PATH.'classes/');

//Loads the installation check
//you need to delete this for better performance @todo use install lock file
/*
if (file_exists('install.php'))
{
	return include 'install.php';
}
*/

//APP BOOTSTRAP
require APP_PATH.'/bootstrap.php';