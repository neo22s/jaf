<?php
/**
* APP configuration loader
* Loads an ini file parses it using vendor iniParser.
* 
* Usage exampple:
* Config::load(APP_PATH.'config.ini');
* $value = Config::get('section','key');
* Config::set('new_section', 'some_key','value');
* Config::save(); //write to disk the new sets
*
* @package     JAF
* @subpackage  Core
* @category    Config
* @author      Chema Garrido <chema@garridodiaz.com>
* @license     GPL v3
* 
*/

class Config{
    
    /**
     * 
     * @config instance of ini parser class
     * 
     */
    private static $config;
    
    /**
     * 
     * @var string file path
     * 
     */
    private static $config_file;
    
    /**
     * 
     * Loads the INI file into an array 
     * @param string $file path to ini
     * 
     */
    public static function load($file)
    {
        if (is_readable($file))
        {
            self::$config_file = $file;
            self::$config = new iniParser(self::$config_file);
        }
        else
        {
            trigger_error('Ini file not readable: '.$file, E_USER_ERROR);
        }
    }
    
    /**
     * 
     * Get the value or a entire section as array.
     * @param string $section
     * @param string $key
     * @return mixed 
     */
    public static function get( $section, $key=NULL )
    {
        return self::$config->get($section, $key);
    }
    
    /**
     * 
     * Get the full config object.
     * @return iniParser
     */
    public static function get_all( )
    {
        return self::$config;
    }
    
    /**
     * 
     * sets value inside a section.
     * @param string $section
     * @param string $key
     * @param string $value
     * @return boolean 
     */
    public static function set( $section, $key, $value=NULL )
    {
        return self::$config->set($section, $key,$value);
    }
    
    /**
     * 
     * saves current config.
     * @param string $file
     * @return boolean
     */
    public static function save($file=NULL)
    {
        if ($file!==NULL)
        {
            self::$config_file=$file;
        }
        return self::$config->save(self::$config_file);
    }
    
    /**
     * 
     * get the config file full path
     * @return string
     */
    public static function get_config_file()
    {
        return self::$config_file;
    }
    
}