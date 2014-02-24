<?php
class config_test extends Test{
    public static $config_file;
    public static $orig_config_file;
    
    public static function load_test()
    {
        //first we save the original config file location to load it after allt he tests ;)
        self::$orig_config_file = Config::get_config_file();
        self::$config_file = __DIR__.'/config_test.ini';

        self::add_note('config_test.ini');
        
        $content = '[test]
notes = somenotes here
version = 0.9 beta
time = 1318784424

[user]
name = chema
surname = some name
age = 27';
        
        H::fwrite(self::$config_file, $content);
        
        Config::load(self::$config_file);
                
        return (get_class(Config::get_all())=='iniParser')? TRUE:FALSE;
    }
    
    public static function get_test()
    {
        $test = Config::get('test');
        if (!is_array($test))
        {
            self::add_note('failed retrieving section test');
            return FALSE;
        }
        self::add_note('retrieving section user->name');
        return (Config::get('user','name')=='chema')? TRUE:FALSE;
    }
    
    public static function set_test()
    {
        self::add_note('set new section and key');
        Config::set('new_section', 'some_key','value');
        return (Config::get('new_section', 'some_key')=='value')? TRUE:FALSE;
    }
    
    public static function save_test()
    {
        Config::set('Tool','time', time());
        if (Config::save()==FALSE)
        {
            self::add_note('failed saving ini file');
            return FALSE;
        }
        
        Config::load(self::$config_file);
        self::add_note('saving temp config file');
        return (is_numeric(Config::get('Tool', 'time')))? TRUE:FALSE;        
    }
    
    public static function unload_test()
    {
        self::add_note('not a method at config class');
        H::remove_resource(self::$config_file);
        //we reload the default config since is a singleton
        Config::load(self::$orig_config_file);
        return !is_readable(self::$config_file);
    }
    
}