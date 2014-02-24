<?php 
defined('BASE_PATH') or die('No direct script access.');

/**
 * This is initially loaded with the theme.
 */

//defaults scripts and css for the entire APP, minified, compressed and merged into 1 file
View::css_add('style_v1',View::template_url().'css/style.css?v=1');//@TODO doesnt work really good.
View::js_add('modernizer',View::template_url().'js/libs/modernizr-1.7.min.js','header',TRUE);
View::js_add('common',View::template_url().'js/script.js','header',TRUE);