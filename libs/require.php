<?php

error_reporting(E_ALL);

define('NAME', 'NameOfTheApp');

define('ROOTPATH', realpath( sprintf( '%s%s..%2$s', dirname(__FILE__), DIRECTORY_SEPARATOR) ) );
define('LIBPATH', realpath(dirname(__FILE__)));
define('DATAPATH', ROOTPATH . DIRECTORY_SEPARATOR . 'data');
//define('DBPATH', DATAPATH . DIRECTORY_SEPARATOR . NAME . '.db');
//define('PDO_DSN', 'sqlite:'.DBPATH);
define('PDO_DSN', 'mysql:host=localhost;dbname=,$user,$password');

require_once(LIBPATH . DIRECTORY_SEPARATOR . 'Data/Data.php');
require_once(LIBPATH . DIRECTORY_SEPARATOR . 'Logs.php');
require_once(LIBPATH . DIRECTORY_SEPARATOR . 'Debug.php');
require_once(LIBPATH . DIRECTORY_SEPARATOR . 'Cache.php');

require_once(LIBPATH . DIRECTORY_SEPARATOR . 'App.php');
//require_once(LIBPATH . DIRECTORY_SEPARATOR . 'Custom/Object.php');

require_once(LIBPATH . DIRECTORY_SEPARATOR . 'toolbox.php');

$locales = array('fr_FR', 'fr_FR.utf8');
putenv('LANG='.implode(',', $locales));
setlocale(LC_ALL, $locales);
bindtextdomain(NAME, LIBPATH . DIRECTORY_SEPARATOR . 'i18n');
bind_textdomain_codeset(NAME, 'UTF8');
textdomain(NAME);

?>