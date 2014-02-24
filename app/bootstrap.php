<?php
/**
 * Application bootstrap
 *
 * @package     JAF
 * @subpackage  Core
 * @category    Bootstrap
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */

//LOADING CORE
require CORE_PATH.'/h.php';

//LOADING APP CONFIGURATION BY ENV @todo
require APP_PATH .'config.php';

//WAS AT INDEX MOVED SINCE IS DYNAMIC DEPENDING ON THE THEME @todo
define('VIEWS_PATH', PUBLIC_PATH.'themes/'.TEMPLATE_NAME.'/views/');

//core startup
H::start();

Config::load(APP_PATH.'config.ini');

//adding routes always from more specific to less
Router::add(array 
                (
                	'page_route' => array
                            (
                                'match'         => '/[any].html',
                                'controller'    => 'example',
                                'action'        => 'page',
                                'cache'			=> 15//7*24*60*60
                            ),
                    'test_route' => array
                            (
                                'match'         => '/more/[alpha]/[num]/[alphanum]/',
                                'controller'    => 'example',
                                'action'        => 'params',
                                'cache'			=> 60*60
                            ),
                     //DO NOT DELETE THIS ROUTE
                     'default' => array
                            (
                                'match'         => '[any]',
                                'controller'    => 'home',//you can change this
                                'action'        => 'index',//and this
                                'cache'			=> FALSE
                            )
                ));
            

//check a match for the routes and saves it internally         
Router::match();
    
/**
 * Load Vendors OPTIONAL. comment those you won't use.
 */
    //start cache
    if (CACHE_ACTIVE)
    {
        //returns the page if was cached
        H::page_cache(Router::$cache);//BE AWARE WE USE FILE CACHE STORAGE FOR THIS OPERATION
        Cache::get_instance(CACHE_TYPE,CACHE_EXPIRE,CACHE_CONFIG);
    }
      
    //language locales
    i18n::load(LOCALE,CHARSET,TIMEZONE);

    //start DB connection Not a vendor
    DB::get_instance(DB_USER,DB_PASS,DB_NAME,DB_HOST,DB_CHARSET,DB_TIMEZONE,DB_PERSISTENT);
    DB::set_cache(CACHE_ACTIVE);

/**
 * Load plugins
 */     

    
/**
 * Template initialization
 */
    H::load_file(VIEWS_PATH.'../init.php'); 

/**
 * Execute the main request. dispatching the matched route
 */
    Router::dispatch();