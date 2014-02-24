<?php
/**
 * Core functions helper
 *
 * @package     JAF
 * @subpackage  Core
 * @category    Helper
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */
class H{ 
        
    /**
     * Core start up
     */
    public static function start()
    {
        spl_autoload_register('H::autoload'); // custom autoload   
        register_shutdown_function('H::end'); //what to do at the end of the script
        
        log::error_reporting(DEBUG);
        log::add();    
        
        //prevent attacks, hacks, injections, xss etc...
        H::clean_request();

        //init session
        H::ob_start();
        session_start();
    }
    
    /*
     * Core finish execution function
     */
    public static function end()
    {
        log::add();
        //saving to cache
        H::page_cache(Router::$cache,'save');
        log::show_logs('HTML');  
        //flush content to browser
        ob_end_flush();
    }
    
    /**
     * Loads the given class
     * @param $class
     * @return boolean found
     */
    public static function autoload($class)
    {        
        
        $class=str_replace('_','/',strtolower($class));
        //log::add('H::autoload | class:'.$class);
        
        //loading controller / model from APP
        if (strpos($class,'controller') || strpos($class,'model'))
        {
            if(H::load_file(CLASSES_PATH.$class.'.php'))
            {
                return TRUE;
            }
        }
        
        //first we try to load the class from the core
        if(H::load_file(CORE_PATH.$class.'.php'))
        {
            return TRUE;
        }
        //trying from the Vendors
        if(H::load_file(VENDOR_PATH.$class.'.php'))
        {
            return TRUE;
        }
        //trying from the APP classes
        if(H::load_file(CLASSES_PATH.$class.'.php'))
        {
            return TRUE;
        }
        //nothing found :S
        return FALSE;
        
    }
    
    /**
     * includes a file to the system
     * @param string $file
     * @param boolean $verify_exists if set false we don't check the file exists and we add it
     */
    public static function load_file($file,$verify_exists=TRUE)
    {
        //check if file exists
        if ($verify_exists==TRUE)
        {
            if(!file_exists($file))
            {
                log::add('FAILED | file '.$file);
                return FALSE;
            }
        }
        
        @require ($file);
        
        log::add('SUCCESS | verify: '.(int)$verify_exists.' | file '.$file);
        
        return TRUE;
    }
    
    /**
     * 
     * Clean all the request for the APP to prevent any injection
     * We preserve the original values from the Requests
     * Later on always to get params  H::$_POST['inputaname']; or P('inputname');
     */
    public static $_POST;
    public static $_GET;
    public static $_COOKIE;
    
    public static function clean_request()
    { 
        self::$_POST   = array_map('H::filter_data', $_POST);
    	self::$_GET    = array_map('H::filter_data', $_GET);
    	self::$_COOKIE = array_map('H::filter_data', $_COOKIE);
    	log::add();
    }

    /**
     * filters the vars recursive
     * @param unknown_type $data
     * @return mixed string cleaned or recursive callback
     */
    public static function filter_data($data)
    {
    	return (is_array($data)) ?  array_map('H::filter_data', $data) : H::clean($data);
    }

    /**
     * string cleaner, to prevent any kind of injection
     * @param string $var
     * @return string variable cleaned
     */
    public static function clean($var)
    {
    	$var = H::nl2br($var);//removing nl
    	if(get_magic_quotes_gpc())
    	{
    	    $var = stripslashes($var); //removes slashes
    	}
    	if(DB::isloaded())
    	{
    	    $var = mysql_real_escape_string($var); //sql injection
    	} 
    	return strip_tags($var,ALLOWED_HTML_TAGS);//whitelist of html tags
    }
    
    /**
     * improved version of nl2br since that one doesnt work really good
     * @param string $var
     * @return string without line returns
     */
    public static function nl2br($var)
    {
    	return str_replace(array('\\r\\n','\r\\n','r\\n','\r\n', '\n', '\r'), '<br />', nl2br($var));
    }

    /**
     * simple header redirect
     * @param string $url to redirect
     */
    public static function redirect($url)
    {
        header('Location: '.$url);//redirect header
        die();
    }
    
    /**
     * generates a string ready to be in the URL / post slug
     * @param string string to replace
     * @return string prepared for the URL
     */
    public static function friendly_url($var)
    {
    	$var = mb_strtolower(H::replace_accents($var),CHARSET);
        $var = str_replace(array('http://', 'https://', 'www.'), '', $var);//erase http/https and wwww, we do shorter the url
        $var = preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('','-',''),$var);
        log::add();
    	return $var;
    }
    
    /**
     * replace for accents catalan spanish and more
     * @param string to replace characters
     * @return string with characters replaced
     */
    public static function replace_accents($var)
    {
        $from = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $to   = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        $var = str_replace($from, $to,$var);
        log::add(); 
        return  $var;
    } 
    
    /**
     * check correct url formation
     * @return boolean
     */    
    public static function is_URL($url)
    {
    	return (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url) > 0)? TRUE:FALSE;
    }
    
    /**
     * session start
     */
    public static function ob_start()
    {
        if (extension_loaded('zlib') && !DEBUG) 
        {//check extension is loaded and debug enabled
            if(!ob_start('ob_gzhandler'))//start HTML compression, if not normal buffer input mode  
            {
                ob_start();
            } 
        }
        else //normal output in case could not load extension or debug mode
        {
             ob_start();
        }
        log::add();
    }
    
 	/**
     * caches the output of the given page
     * used in the bootstrap if cached enabled
     * @param boolean $cached says if we should cache the page or not
     * @param string $action
     */
    public static function page_cache($cache_expire=NULL,$action='start')
    {
        if (is_numeric($cache_expire)) 
        {
            log::add('cache: '.$cache_expire.' action: '.$action);
            //we use file cache since I think for an HTML page is the best storage, you can chage this of course ;)
            $cache = Cache::get_instance(CACHE_TYPE,$cache_expire,CACHE_CONFIG); 
            if ($action=='start')
            {
            	$html = $cache->cache(H::get_current_URI());
            	if ($html !==NULL)
            	{
            	    die(gzuncompress($html));
            	} 
            }
            elseif($action=='save')
            {
                echo '<!--Page cached on '.date('d-m-Y-H:i:s').' expires on '.date('d-m-Y-H:i:s',time()+$cache_expire).' -->';//this is just a bit dirty @TODO
                $cache->cache(H::get_current_URI(),gzcompress(ob_get_contents()));  
            }	
            unset($cache);
        }
        
    }
    
    /**
     * 
     * Get the real ip form the visitor.
     * @param boolean returns ip to long instead of string ip
     * @return string IP
     */
    public static function get_ip($to_long=FALSE)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
     
        return ($to_long==TRUE)? ip2long($ip):$ip;
    }
    
    /**
     * gets the current URI 
     * @return string current URI
     */
    public static function get_current_URI()
    {
        return H::get_domain().$_SERVER['REQUEST_URI'];
        //return SITE_URL.'/'.Router::$controller.'/'.Router::$action.'/'.implode('/',Router::$params);
    }
    
    /**
     * get the domain with or without protocol
     * @param boolean $url
     * return string domain
     */
    public static function get_domain($url=TRUE)
    {
        if ($url)
        {
            if (defined(SITE_URL))//allow to override the url
            {
                return SITE_URL;
            }
            //we try to guess the domain name
            else
            {
                return 'http://'.$_SERVER['SERVER_NAME'];   
            }
            
        }
        //only name
        else 
        {
            return $_SERVER['SERVER_NAME'];
        }
        
    }
    
	/**
     * checks if a call_back function name can be used
     * @param string $call_back function name
     * @return boolean
     */
    public static function is_callable($call_back)
    {
        if (function_exists($call_back))
        {
            return TRUE;
        }
        
        //for static methods, be aware this may be not the best way and we need to trust the developers
        if (strpos($call_back, '::'))
        {
            $m=explode('::',$call_back);
            if (method_exists($m[0], $m[1]))
            {
                return TRUE;
            }
        } 
       
       return FALSE;
    }
    
    /**
     * write to file
     * @param $filename fullpath file name
     * @param $content
     * @return boolean
     */
    public static function fwrite($filename,$content)
    {
        log::add('filename:'.$filename);
        $file = fopen($filename, 'w');
	    if ($file)
	    {//able to create the file
	        fwrite($file, $content);
	        fclose($file);
	        return TRUE;
	    }
	    return FALSE;   
    }//@TODO create intermediate directories if needed
    
    /**
     * read file content
     * @param $filename fullpath file name
     * @return $string or false if not found
     */
    public static function fread($filename)
    {
        log::add('filename:'.$filename);
        if (is_readable($filename))
        {
            $file = fopen($filename, 'r');
    	    if ($file)
    	    {//able to read the file
    	        $data = fread($file, filesize($filename));
    		    fclose($file);
    	        return $data;
    	    }
        }
	    return FALSE;   
    }
    
    
	/**
     * allows to delete a directory or file recursevely	
     * @param string path/file
     * @param integer filters the fiels to delte by age
     */
	public static function remove_resource($_target=NULL,$older_than=0) 
    {
        //file?
        if( is_file($_target) ) 
        {
            if( is_writable($_target)  && time() >= (filemtime($_target) + $older_than) ) 
            {
                
                if( @unlink($_target) ) 
                {
                    return TRUE;
                }
            }
            return FALSE;
        }
        //dir recursive
        if( is_dir($_target) ) 
        {
            if( is_writeable($_target) ) 
            {
                foreach( new DirectoryIterator($_target) as $_res ) 
                {
                    if( $_res->isDot() ) 
                    {
                        unset($_res);
                        continue;
                    }
                    if( $_res->isFile() ) 
                    {
                        self::remove_resource( $_res->getPathName() );
                    }

                    elseif( $_res->isDir() ) 
                    {
                        self::remove_resource( $_res->getRealPath() );
                    }
                    unset($_res);
                }
                if( @rmdir($_target) && time() >= (filemtime($_target) + $older_than) ) 
                {
                    return TRUE;
                }
            }
            return FALSE;
        }
    }
    
    /**
     * gets the extension from a string
     * @param string $file
     * @return string extension name
     */
    public static function get_extension($file)
    {
        $dots = explode('.', $file);
        $extension = end($dots);
        return $extension;
    }
    
    /**
     * Simple function to send an email
     * 
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $body
     * @param string $extra_header
     * @return boolean
     */
    public static function email($to,$from,$subject,$body,$headers=NULL)
    {
        //we add hook just in case we want to over wirete the email function
        //example    Hook::add_action('h::email','some_function_used_instead_H::email');
        if (Hook::exists_action('H::email'))
        {
            return Hook::do_action('H::email',func_get_args());
        }
        else 
        {
            if ($headers==NULL)
            {
                $headers = 'MIME-Version: 1.0' . PHP_EOL;
                $headers.= 'Content-type: text/html; charset='.CHARSET. PHP_EOL;
                $headers.= 'From: '.$from.PHP_EOL;
                $headers.= 'Reply-To: '.$from.PHP_EOL;
                $headers.= 'Return-Path: '.$from.PHP_EOL;
                $headers.= 'X-Mailer: PHP/' . phpversion().PHP_EOL;
            }
            
            return mail($to,$subject,$body,$headers);
        }
    }
    
}

/**
 * shared common functions 
 */

    /**
     * request get alias
     * @param $name
     */
    function G($name)
    {
    	return H::$_GET[$name];
    }
    
    /**
     * request post alias
     * @param $name
     */
    function P($name)
    {
    	return H::$_POST[$name];
    }