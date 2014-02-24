<?php
/**
 * View class
 *
 * @package     JAF
 * @subpackage  Core
 * @category    View
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 * 
 * Usage example:
 * $v=new View('log');//factory
 * $v->some_var='value';
 * $v->anothervar='available in the template';
 * $v->render();//displays the view
 * 
 * SEO metas:
 * $v->meta_title='title for your page';
 * $v->meta_description='the description for your page';
 * $v->meta_keywords='some,keywords,for,your,page';
 * If no description or keyword given will try to figure out the content using the value of $content
 * $v->content='from this text we will extract the keywords and the description for your test if they where not given before';
 * 
 * Adding JavaScript to templates:
 * echo View::js_tag('ie-png-fix',View::template_url().'js/libs/dd_belatedpng.js',FALSE);
 * Adding  minified JavaScript to your templates:
 * View::js_add('unique_name','URLSCRIPT.js','footer',TRUE);
 * View::js_add('anotherJS','URLSCRIPT2.js','header',TRUE);
 * In your template:
 * View::js_return('header');
 * View::js_return('footer');
 * 
 * Adding CSS to your template
 * View::css_add('uniqueCSSname','admin-bar.css?ver=20110622');
 * View::css_add('uniqueCSSname2','admin-bar.css?ver=20110622');
 * View::css_return();
 */
class View
{	
    private $data = array();//variables that are later available to the view
    private static $js  = array();//js array to minify and merge
    private static $css = array();//css array to minify and merge
    private $view_name;//view that is gonna be loaded
    
    /**
     * Sets the view we need to render
     * @param string $view_name
     */
    public function __construct($view_name)
    {   
        log::add($view_name);
        if(file_exists(VIEWS_PATH.$view_name.'.php'))
        {
             $this->view_name=$view_name;			
        }
		else
		{
		    $this->view_name='home';//@todo set default in config?
		    log::add('View::construct view not found: '.$view_name);
		}
    }
    
    /**
     * "Renders" the view and extracts the variables.
     */
    public function render()
	{
	    log::add('start | '.$this->view_name);
        
	    //SEO metas
        if (!array_key_exists('meta_title', $this->data) || 
            !array_key_exists('meta_description', $this->data) || 
            !array_key_exists('meta_keywords', $this->data) )
        {
            $seo = new phpSEO();
            $seo->setCharset(CHARSET);
            //trying to get some content....
            if (isset($this->content))
            {
                $seo->setText($this->content);
            }
            elseif (isset($this->meta_description))//not that bad
            {
                $seo->setText($this->meta_description);
            }
            elseif (isset($this->meta_title))//desperate!
            {
                $seo->setText($this->meta_title);
            }
            
            //meta view
            if (!array_key_exists('meta_title', $this->data))
            {
                $this->meta_title  ='Title for this View missing';
            }
            //meta description
            if (!array_key_exists('meta_description', $this->data))
            {
                $this->meta_description = $seo->getMetaDescription();
            }
            //meta keyword
            if (!array_key_exists('meta_keywords', $this->data))
            {
                $this->meta_keywords = $seo->getKeywords();
            }
            
            unset($seo);
        }

        //load variables to the view from controller
	    extract ($this->data);
	    //includes the view ;)
        require VIEWS_PATH.$this->view_name.'.php';
        
        log::add('finish | '.$this->view_name);
	}
	
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return (array_key_exists($name, $this->data)) ? $this->data[$name] : NULL;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
    
    /**
     * gets the right url for the template in the public
     * @return string
     */
    public static function template_url()
    {
        return SITE_URL.'/themes/'.TEMPLATE_NAME.'/';
    }
    
    //JS tools
    
    /**
     * Adds a JS file to the pool of scripts
     * @param string $name
     * @param string $file, file to add in the JS pool
     * @param sring $container where the script should be placed holder name
     * @param boolean $parse, parses for minify and merging
     */
    public static function js_add($name,$file,$container='header',$parse=TRUE)
    {
        log::add('name:'.$name.' | file:'.$file.' | container:'.$container);
        if (!array_key_exists($name,View::$js))//name is unique
        {
            View::$js[$name] = array('file'=>$file,'container'=>$container,'parse'=>$parse);
        }
    }
    
    /**
     * prints the js files
     * @param string $container
     */
    public static function js_return($container='header')
    {
        $to_minify = array();
        foreach(View::$js as $js_name=>$js)
        {
            if($js['container']==$container)
            {
                if ($js['parse']===TRUE)
                {
                    $to_minify[]=$js_name;
                }
                else//not minified or merged
                {
                    echo View::js_tag($js_name,$js['file']);
                }
            }
        }
        log::add('to minify:'.count($to_minify));
        //minification and merging
        if(count($to_minify)>0)
        {
            $js_minified_name='jsmin-'.implode('-',$to_minify).'.js';
            $js_file=PUBLIC_PATH.'themes/'.TEMPLATE_NAME.'/minified/'.$js_minified_name;
            
            if (!file_exists($js_file))//@todo maybe check expire?
            {
                $js_minified='';
                //check cache name file
                foreach ($to_minify as $js_name)
                {
                    $js_minified.=file_get_contents(View::$js[$js_name]['file']);
                }
                //write contents and minify
                H::fwrite($js_file,JSMin::minify($js_minified));
            }
            echo View::js_tag($js_minified_name,View::template_url().'minified/'.$js_minified_name);
            log::add('finish minify');
        }        
    }
        
    /**
     * creates an html script
     * @param string $name
     * @param string $file
     * @param boolian $async, creates different script for asynchronous
     * @return string
     */
    public static function js_tag($name,$file,$async=TRUE)
    {
        if ($async)
        {
           return '<script id="js-'.$name.'" type="text/javascript">
                      (function() {
                        var sc = document.createElement(\'script\'); sc.type = \'text/javascript\'; sc.async = true;
                        sc.src = \''.$file.'\';
                        var s = document.getElementById(\'js-'.$name.'\'); s.parentNode.insertBefore(sc, s);
                      })();
                      </script>';
        }
        return '<script id="js-'.$name.'" src="'.$file.'"></script>';
    }
    
    //CSS tools
	/**
     * Adds a css file to the array
     * @param string $name
     * @param string $file, file to add in the css pool
     * @param boolean $parse, parses for minify and merging
     */
    public static function css_add($name,$file,$parse=TRUE)
    {
        log::add('name:'.$name.' | file:'.$file);
        if (!array_key_exists($name,View::$css))//name is unique
        {
            View::$css[$name] = array('file'=>$file,'parse'=>$parse);
        }
    }
    
    /**
     * prints the css
     */
    public static function css_return()
    {
        $to_minify = array();
        foreach(View::$css as $css_name=>$css)
        {
            if ($css['parse']===TRUE)
            {
                $to_minify[]=$css_name;
            }
            else//not minified or merged
            {
                echo View::css_tag($css_name,$css['file']);
            }
        }
        log::add('to minify:'.count($to_minify));   
        //minification and merging
        if(count($to_minify)>0)
        {
            $css_minified_name='cssmin-'.implode('-',$to_minify).'.css';
            $css_file=PUBLIC_PATH.'themes/'.TEMPLATE_NAME.'/minified/'.$css_minified_name;
            
            if (!file_exists($css_file))//@todo maybe check expire?
            {
                $css_minified='';
                //check cache name file
                foreach ($to_minify as $css_name)
                {
                    $css_minified.=file_get_contents(View::$css[$css_name]['file']);
                }
                //write contents and minify
                H::fwrite($css_file,MinifyCSS::process($css_minified));
            }
            echo View::css_tag(View::template_url().'minified/'.$css_minified_name);
            log::add('finish minify');
        }        
    }
        
    /**
     * creates the rel style sheet
     * @param string $file
     * @return string
     */
    public static function css_tag($file)
    {
        return '<link rel="stylesheet" href="'.$file.'">';
    }
}