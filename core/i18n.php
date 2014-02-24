<?php
/**
 * i18n class
 *
 * @package     JAF
 * @subpackage  Core
 * @category    Halper
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */

class i18n{
    
    /**
     * Loads the gettext dropin for the locales
     * @param string $locale
     * @param string $charset
     */
    public static function load($locale=NULL,$charset=NULL,$timezone=NULL)
    {
        /**
         * Set the default time zone.
         *
         * @see  http://php.net/timezones
         */
        date_default_timezone_set($timezone);
        
        
        //LOCALES
        mb_internal_encoding($charset);
        mb_http_output($charset);
        mb_http_input($charset);
        mb_language('uni');
        mb_regex_encoding($charset);
        
        //gettext override
        H::load_file(VENDOR_PATH.'gettext/gettext.inc',FALSE);
    
        if ( !function_exists('_') )
        {//check if gettext exists if not use dropin
            T_setlocale(LC_MESSAGES, $locale);
            bindtextdomain(BIND_DOMAIN,LOCALES_PATH);
            bind_textdomain_codeset(BIND_DOMAIN, $charset);
            textdomain(BIND_DOMAIN);
            log::add('i18n::load dropin locale: '.$locale.' charset: '.$charset);
        }
        else
        {//gettext exists using fallback in case locale doesn't exists
            T_setlocale(LC_MESSAGES, $locale);
            T_bindtextdomain(BIND_DOMAIN,LOCALES_PATH);
            T_bind_textdomain_codeset(BIND_DOMAIN, $charset);
            T_textdomain(BIND_DOMAIN);
            log::add('i18n::load locale: '.$locale.' charset: '.$charset);
        }
        //end language locales
    }
}

/**
 * Echoes a text and tries to translate it
 * @param string $text
 */
function _e($text)
{
    if (function_exists('T_'))
    {    
        echo T_($text);
    }
    else
    {
        echo $text;
    }
}
    