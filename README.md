JAF PHP		Simple MVC
===

Just Another Framework. Developed in 2010 for learning purposes, do not use in production.

===
 	
RELEASE 	0.1 (2010-xx-xx)
AUTHORS 	Chema Garrido neo22s@gmail.com
LICENSE		GPL v3 http://www.gnu.org/licenses/gpl-3.0.html

VENDORS AND EXTRAS
Benchamarking	David Constantine Kurushin 
PHP-Gettext		http://launchpad.net/php-gettext/
JSmin			http://jsmin.php
Minify CSS		http://code.google.com/u/1stvamp/
iniParser		http://www.re-design.de
Sorttable		http://www.kryogenix.org/code/browser/sorttable/
jQuery			http://jquery.com/
humanstxt		http://humanstxt.org/
initializr		http://initializr.com/

BASED ON PREVIOUS WORKS
phpSeo			http://neo22s.com/phpseo/
fileCache		http://neo22s.com/filecache/
wrapperCache	http://neo22s.com/wrappercache/
phpMyDB			http://neo22s.com/phpmydb/

REQUIREMENTS
PHP 5.2+
Optional:
mod_rewrite
MySQL 	(you can disable it on the bootstrap file)
GetText (you can disable it on the bootstrap file)


INSTALLING & CONFIG
1 - Create a new virtual host, example:
	<VirtualHost *:80>
	ServerName yourdomain.com
	DocumentRoot /jaf/
	</VirtualHost>

2 - Check that /app/config.php has the right values.
3 - Run yourdomain.com to check for dependencies
4 - Delete or rename /jaf/install.php
5 - If you want to use mod_rewrite in apache rename /jaf/example.htaccess to /jaf/.htaccess



TODO for 0.1
config.ini instead config.php
db and config not singleton, register pattern better
url without modrewrite, index.php/some/route/here/ check at get url() if mod rewirte off return with index.php
at every class add usage examples in the top ,db,cache,view...
Form helper CSFR, captcha, validation & js
install in folder / generate simple htaccess on install. /after install remove install.lock file
views path may be possible to be changed and not using a define.
use less defines inside core functions
php doc guide like KO

v1.0
plugin system using hooks (add unittest as plugin)
Code generator mysql class generator, using model Base scaffolding?
check if php-gettext can be loaded as a plugin hook
