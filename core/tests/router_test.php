<?php
class router_test extends Test{
    
    public static function get_uri_path_test()
    {
        $path=Router::get_uri_path();
        self::add_note($path);
        return ($path!==FALSE) ? TRUE : FALSE;
    }
    
    public function get_uri_params_test() 
    {
        $_SERVER['REQUEST_URI']='/controller/action/values/1/2/';
        $path   = Router::get_uri_path();
        $params = Router::get_uri_params($path);
    	self::add_note(count($params));
    	return (count($params)==5)?TRUE:FALSE;
    }
    
    public function replace_tags_test()
    {
        $rep=Router::replace_tags('/[any]]/[alpha]/[num]/[alphanum]/');
        self::add_note($rep);
		return ($rep=='/(.+)]/([a-z]+)/([0-9]+)/([a-z0-9]+)/')?TRUE:FALSE;
    }
    
    public function add_routes_test()
    {
        self::add_note('3 routes added');
        Router::add(array 
                (
                	'page_route' => array
                            (
                                'match'         => '/[any]-[num].html',
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
        return (count(Router::$routes)==3)?TRUE:FALSE;
    }
    
    
    public function get_url_test()
    {
        $res=TRUE;
        $url = Router::get_url('default',array('controller','action','param1','param2','param3'));
        self::add_note($url);
        if ($url!==H::get_domain().'/controller/action/param1/param2/param3/')
        {
            $res=FALSE;
        }
        if($res)
        {
            $url = Router::get_url('test_route',array('controller',1323,'param1'));
            self::add_note($url);
            if ($url!==H::get_domain().'/more/controller/1323/param1/')
            {
                $res=FALSE;
            }
        }
        return $res;
    }
    
    
    public function match_url_route_test()
    {
        $res=TRUE;
        
        //based on the routes added on previous test function add_test
        
        //first match generic MVC
        $_SERVER['REQUEST_URI']='/controller/action/values/1/2/';
        Router::match();
        if (Router::$route_name!='default')
        {
            $res=FALSE;
        }
        
        //testing second match [any]-[num].html
        if ($res)
        {
            $_SERVER['REQUEST_URI']='/example_page-8.html';
            Router::match();
            if (Router::$route_name!='page_route')
            {
                $res=FALSE;
            }
        }
       
        //testing third match /more/[alpha]/[num]/[alphanum]/
        if ($res)
        {            
            $_SERVER['REQUEST_URI']='/more/alphaonly/45565/alphaandnum1/';
            Router::match();
            if (Router::$route_name!='test_route')
            {
                $res=FALSE;
            }
        }
        
        self::add_note(Router::$route_name.' : '.$_SERVER['REQUEST_URI']);
        return $res;
    }
    
}