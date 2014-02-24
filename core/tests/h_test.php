<?php
class h_test extends Test {
    
    public function __construct()
    {
        
    }
    
    public function autoload_test()
    {
        spl_autoload_register('H::autoload');
        //locate core file
        $hook = new Hook();
        //locate vendor
        $seo = new phpSEO();
        self::add_note('loaded a core file and a vendor');
        //if there's no fail
        return TRUE;
    }
    
  /*  public function load_file_test()
    {
        self::add_note('loaded file view.php');
        return (H::load_file(CORE_PATH.'/view.php'))?TRUE:FALSE;
    }*/
    
    public static function clean_request_test()
    { 
        $_POST['bad'] = '<script>alert(\'asdasd\')</script>';
        H::clean_request();
        $res = P('bad');
        self::add_note('clean bad var:'. $res);
        return ($res=="alert(\'asdasd\')")?TRUE:FALSE;
    }
    
    public static function nl2br_test()
    { 
        $res= '---\r\n--here';
        self::add_note('nltobr: '. $res);
        $res = H::nl2br($res);
        return ($res=='---<br />--here')?TRUE:FALSE;
    }
    
    public function friendly_url_test()
    {
        $res = H::friendly_url('helló there!');
        self::add_note('helló there!='.$res);
        return ($res=='hello-there')? TRUE:FALSE;
    }
    
    public static function is_url_test()
    { 
        self::add_note('http://open-classifieds.com');
        return (H::is_URL('http://open-classifieds.com'))?TRUE:FALSE;
    }

    public static function is_callable_test()
    { 
        self::add_note('H::end && P');
        return (H::is_callable('H::end') && H::is_callable('P'))?TRUE:FALSE;
    }
    
    public function file_actions_test()
    {   
        $file=__DIR__.'/file.txt';
        self::add_note($file);
        H::fwrite($file,'some content here');
        $res = H::fread($file);
        if ($res=='some content here')
        {
            //since the content is there now we delete and we test the remove resource
            H::remove_resource($file);
            return (H::fread($file)==FALSE)?TRUE:FALSE;
        }
        return FALSE;
    }
    
    public function get_extension_test()
    {
        self::add_note('some_file.txt');
        return (H::get_extension('some_file.txt')=='txt')?TRUE:FALSE;
    }
    
    public function email_test()
    {
        self::add_note('hrkrvgyp@sharklasers.com');
        return H::email('hrkrvgyp@sharklasers.com','jaf@localhost','Subject for JAF unit test '.date('Y-m-d H:i:s'),'Body test');
    }
}