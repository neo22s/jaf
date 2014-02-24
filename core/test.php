<?php
/**
 * Class for JAF unit testing
 *
 * @package     JAF
 * @subpackage  Core
 * @category    Test
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */
      
class Test{
    
    protected static $tests_results;//array of results
    protected $tests_path;
    
    public function __construct()
    {
        
    }
    /**
     * 
     * @param string $folder
     */
    public function run_all($folder=NULL)
    {    
            
        if ($folder==NULL)
        {            
            $folder=$this->tests_path;
        }
        else
        {
            $this->set_path($folder);
        }
                
        //read all the files in the TESTS folder and include them
        if(is_dir($folder)) 
        {
            foreach( new DirectoryIterator($folder) as $file ) 
            {
                if (H::get_extension($file)=='php' && strpos( $file, 'test' ) !== FALSE)
                {
                    H::load_file($folder.$file);
                    $class=substr($file,0,-4);// echo $folder.$file.' class: '.$class.'-';
                    $this->run($class);
                } 
            }
        } 	
        //no folder specified so take class from the caller
        else
        {
            $class = get_class( $this );
            $this->run($class);
        }
    	
    }
    
    

	/**
     * 
     * @param string $class
     */
    public function run($class=NULL)
    {
        if ($class==NULL)
        {
            $class = get_class( $this );
        }
        
        //check is form the rigght type
        if (is_subclass_of(new $class,'Test'))
        {
            $functions = get_class_methods($class);
            $o = new $class;
     		foreach ( $functions as $function )
     		{
     			if( strpos( $function, 'test' ) !== FALSE )
     			{
    		 		$time_start = log::show_timer();
     				//$result     = call_user_func( array( $o, $function ) );
     				$result = $o->$function();
     				//$result .= print_r($o,1).' class: '.$class.'- function:'.$function.'<br />';
     				//echo $result;//die();
    				$time_end   = log::show_timer();
    				Test::add_result($class,$function,$result, ( $time_end - $time_start ),self::$notes);
    	        }
    	    }
        }
                         
    }
    
    
    /**
     * 
     * @param string $class
     * @param string $function
     * @param string $result
     * @param string $time
     * @param string $notes
     */
    public static function add_result($class,$function,$result,$time,$notes)
    {
		self::$tests_results[$class][$function]['function'] = $function;
		self::$tests_results[$class][$function]['status']   = ( $result === TRUE ) ? TRUE : FALSE;
		self::$tests_results[$class][$function]['time']     = $time;
		self::$tests_results[$class][$function]['notes']    = $notes;
		self::unset_note();
    }
    
    /**
     * Notes
     * @var string
     */
    private static $notes;
    
    /**
     * 
     * @param string $msg
     */
    public static function add_note($msg)
    {
        self::$notes=$msg;
    }
    /**
     * unsets the $note var
     */
    public static function unset_note()
    {
        self::$notes='';
    }
    
    /**
     * sets tests path
     * @param string $path
     */
    public function set_path($path)
    {
        $this->tests_path=$path;
    }
    
    /**
     * Tests View
     * @param string $mode
     */
    public function get_results($mode='HTML')
    {        
        if($mode=='dump')
        {
            var_dump(self::$tests_results);
        }
        elseif($mode=='HTML')
        {
            $values['tests_results']=self::$tests_results;
            extract($values);
            require $this->tests_path.'results.php';
            die();
        }
    }
    
    
}