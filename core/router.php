<?php
/**
 * MVC Router dispatcher
 * 
 * Usage example:
 * 
 * Adding routes: always from more specific to less
 * 
 * Examples:
 * Router::add(array 
                (
                	'unique_route_name' => array
                            (
                                'match'         => '/[any]-[num].html'//Allowed wild cards to match [any], [alphanum], [num], [alpha]
                                'controller'    => 'some_controller',
                                'action'        => 'public_function',
                                'cache'			=>  15//seconds the cache expires
                            ),
                     //DO NOT DELETE THIS ROUTE, REQUIRED:
                     'default' => array
                            (
                                'match'         => '[any]',
                                'controller'    => 'home',//you can change this
                                'action'        => 'index',// and this
                                'cache'			=>  FALSE
                            )
                ));
 * check a match for the routes and saves it internally         
 * Router::match();
 * dispatching the matched route
 * Router::dispatch();
 * 
 * get a url:
 * echo Router::get_url('unique_route_name',array('somepage',677));//uses the route
 * echo Router::get_url('default',array('somepage',677,'alphaor9090'));//returns the default MVC
 * 
 * @package     JAF
 * @subpackage  Core
 * @category    Helper
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */

class Router{
    
    public static $routes=array();//array of routes stored
    public static $route_name;
    public static $controller;
    public static $action;//function
    public static $cache;//cache used at H::page_cache
    public static $params=array();//params sent to the function
    
    /**
     * ads routes arrays to the static var
     * @param array routes
     */
    public static function add($routes)
    {
        foreach($routes as $name=>$route)
        {
            $routes[$name]['match_replaced'] = self::replace_tags($route['match']);
        }
        //array merge so we can add routes whenever we want
        self::$routes=array_merge(self::$routes,$routes);
    }
    
    /**
     * tries to find a match for the current URI
     * 
     */
	public static function match()
	{
	    log::add();
	    //defaults in case no URL
	    self::$route_name= 'default';
        self::$controller= self::$routes[self::$route_name]['controller'];
        self::$action    = self::$routes[self::$route_name]['action'];
        self::$cache     = self::$routes[self::$route_name]['cache'];
        
        //URI path
        $path = Router::get_uri_path(); //var_dump($path);  
        if ($path!==FALSE)
        {//there's URI with params
            foreach(self::$routes as $name=>$route)
            {
                if($name!=='default')//we dont need to check the default match
                {
                    if (preg_match('#^' . $route['match_replaced'] . '$#u', $path, $params))
        			{//there's a match
        			    log::add('pregmatch found: '.$name.' | URI: '.$route['match']);
        			    self::$route_name= $name;
        			    self::$controller= $route['controller'];
                        self::$action    = $route['action'];
                        self::$cache     = $route['cache'];
                        array_shift($params);
                        self::$params    = $params;
                        break;
        			}
                }
            }
            
            //not found a match
            if (self::$route_name==='default')//try default MVC /controlerr/action/param1/param2/param3/..
            {
                $params = Router::get_uri_params($path);
                if (count($params)>0)//there at least 1 parameter in the URI
                {
                    self::$controller= array_shift($params);//first parameter controller
                    self::$action    = (count($params)>0)? array_shift($params):'index';//second parameter action,if not by default index
                    self::$params    = (count($params)>0)? $params:FALSE;//if theres more than 2 params
                }
            }
            
        }
        
        log::add('controller: '.self::$controller.' | action: '.self::$action.' | cache: '.print_r(self::$cache,1).' | params: '.print_r(self::$params,1));
	}
	
	
	/**
     * Dispatch the routes to the right controller/action and with params
     * 
     */
    public static function dispatch()
    {
        $class = 'Controller_'.self::$controller;//controller name
        
        if(class_exists($class))
        {
            //instance of the controlles Class
            $o = new $class;
            
            if(!method_exists($o, self::$action)) //method doesnt exist use index
            {
               self::$action = 'index';
            }
            
            log::add('start controller: '.self::$controller.' | action: '.self::$action.' |  params: '.print_r(self::$params,1)); 
            
            //before specific action hooks
            if (CORE_HOOKS) 
            {
                Hook::do_action($class.'::'.self::$action.'_before');    
            }
            
            //before any action in the controller
            call_user_func(array($o,'before'));
            
            //current action
            if (CORE_HOOKS && Hook::exists_action($class.'::'.self::$action))
            {//theres a hook for that action/controller, we call the hook instead the main action
                Hook::do_action($class.'::'.self::$action);
            }
            else//theres not any hook for that action in the controller
            {
                                
                if (is_array(self::$params))
                {
                    call_user_func_array(array($o,self::$action),self::$params);
                }
                else
                {
                    call_user_func(array($o,self::$action));
                }
                
                log::add('finished controller: '.self::$controller.' | action: '.self::$action.' |  params: '.print_r(self::$params,1));
            }   
            //after any action in this controller
            call_user_func(array($o,'after'));
            
            //after specific action hooks
            if (CORE_HOOKS) 
            {
                Hook::do_action($class.'::'.self::$action.'_after'); 
            }
            unset($o);
        }
        else//if controller doesnt exist 404 @todo depends on home controller?
        {
            log::add('not found: controller: '.self::$controller.' | action: '.self::$action.''); 
            Controller_home::not_found(Router::get_uri_path());   
        }      
    }

	/**
     * From the request URI we get the path
     * @return string path of the URL
     */
    public static function get_uri_path()
    {
        $uri = parse_url(str_replace('/index.php','',$_SERVER['REQUEST_URI']));
        $path = mb_strtolower($uri['path'],CHARSET);
        log::add('path: '.$path); 
        return (!empty($path)) ? $path : FALSE;
    }
    
    /**
     * get the params set in a URL for the default rewrite
     * @param integer $max_params
     * @return array params found at URI
     */
    public static function get_uri_params($path, $max_params=20) 
    {
    	$params = array();
    	$i=0;	
    	foreach( explode('/', $path) as $p) 
    	{
    		if ($i>=$max_params) return $params;//max params reached
    		
    		if ($p!='') 
    		{
    			$params[] = mb_strtolower(H::clean($p));//all the params are cleaned
    			$i++;
    		}
    	}
    	log::add('params: '.print_r($params,1)); 
    	return $params;
    }
    
    /**
     * replaces URI tags for regex
     * @param string $route with tags to be replaced
     * @return string route with the replaced tags
     */
    public static function replace_tags($route)
    {
        $wildcard = array('[any]', '[alphanum]',  '[num]',    '[alpha]');
		$regex    = array('(.+)' , '([a-z0-9]+)', '([0-9]+)', '([a-z]+)');
		$route_r  = str_replace($wildcard, $regex, $route);
		log::add($route.'--->'.$route_r);
		return $route_r;
    }
    
    /**
     * returns an url given a route name and the prams to from it
     * @param string $route_name
     * @param array $params
     * @return string url
     */
    public static function get_url($route_name='default',$params=NULL)
    {
        log::add();
       
        //default MVC
        if($route_name=='default' && is_array($params) )
        {
            log::add('only params:'.print_r($params,1));
            return H::get_domain().'/'.implode('/',$params).'/';
        }
        //mvc for the current view
        elseif( $route_name=='self' && is_array($params) && isset(self::$route_name) )
        {
            log::add('only params for route: '.self::$route_name.'  params:'.print_r($params,1));
            return H::get_domain().'/'.self::$controller.'/'.self::$action.'/'.implode('/',$params).'/';
        }
        /**
         * check if route exists
         * strpos of ( and ) needs to be and ( before ) then replace from ( pos to ) pos with the value
         */
        elseif ( array_key_exists($route_name,self::$routes) && is_array($params) )
        {
            log::add('url for route: '.$route_name.' | params to replace'.print_r($params,1));
            
            self::$replace_params=$params;//sets the static value with the params to replace
            //@todo maybe use an array of matches?? like in function Router::replace_tags
            $url = preg_replace_callback('/\[[a-z]+\]/','Router::replace_matches_to_params', self::$routes[$route_name]['match']);
            
            log::add('final url: '.$url);
            return H::get_domain().$url;
        }
        
        //if not any route, we use by default the current one
        log::add('not any param');
        return H::get_current_URI();      
        
    }
    
    /**
     * static variable to replace the matches with this values Array
     * @todo improve this since may not be the most elegant way
     */
    public static $replace_params;
    
    /**
     * replaces the value of a string for the value of an array
     * used in the Router::get_url function as a callback for the preg_replace_callback function
     * 
     * @param string $matches the match from the callback
     * @return string replaced value
     */
    public static function replace_matches_to_params($matches)
    {
        //value to return instead
        $ret = self::$replace_params[0];
        //we delete it form the array so we have next ;)
        array_shift(self::$replace_params); 
        return $ret;
    }

    
}