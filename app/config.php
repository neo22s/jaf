<?php
/*
 * APP CONFIG @TODO SOMETHING BETTER WITH ENVIRONMENTS
 */

//Initial defines
define('TEMPLATE_NAME', 'default');//change to your favorite theme


//Site Settings
define('ANALYTICS',FALSE);//your g.analytics code here
define('SITE_URL','http://mvc.lo');
define('SITE_NAME','JAF PHP');
define('ALLOWED_HTML_TAGS','');//tags allowed to be inserted, used in strip_tags function
//define('ALLOWED_HTML_TAGS','<b><i><u><div><center><blockquote><li><ul><a><p><br><br />');


//environment settings
define('ENV','DEV');

if (ENV=='DEV')
{
    define('DEBUG',TRUE);//profiler, error notification...
    define('EMAIL_ERROR','neo22s@gmail.com');//email in case fatal error report

    //DB config
    define('DB_HOST','localhost');
    define('DB_USER','root');
    define('DB_PASS','');
    define('DB_NAME','oc');
    define('DB_CHARSET','utf8');
    define('DB_PREFIX','oc_');
    define('DB_TIMEZONE',FALSE);//if false tries to use php date('P') in db.php
    define('DB_PERSISTENT',FALSE);
    
    //cache settings
    define('CACHE_ACTIVE',TRUE);
    define('CACHE_TYPE','filecache');
    //define('CACHE_TYPE','apc');
    define('CACHE_CONFIG',APP_PATH.'cache/');
    define('CACHE_EXPIRE',24*60*60);

    //i18n config
    define('CHARSET','UTF-8');//html charset
    define('LOCALE','en_EN');
    define('BIND_DOMAIN','messages');
    define('TIMEZONE','Europe/Madrid');// @see  http://php.net/timezones
}
