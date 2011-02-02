<?php

error_reporting(E_ALL);

define('NAME', 'Delinquance');

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


function getParam($name)
{
	if( array_key_exists($name, $_POST) )
	{
		return $_POST[$name];
	}
	else if( array_key_exists($name, $_GET) )
	{
		return $_GET[$name];
	}
	return false;
}

function hasParam($name)
{
	return ( getParam($name) !== false );
}

function return_bytes($val)
{
	$val = trim($val);
	$last = strtolower(substr($val, -1));

	if($last == 'g')
	    $val = $val*1024*1024*1024;
	if($last == 'm')
	    $val = $val*1024*1024;
	if($last == 'k')
	    $val = $val*1024;

	return $val;
}

function slug($string, $space = "-") {
	if (function_exists('iconv')) {
		$string = @iconv('UTF-8', 'ASCII//TRANSLIT', $string);
	}
	else
	{
		$string = strtr($string,'éèëêàäâùüûöôïïüûç','eeeeaaauuuooiiuuc');
	}
	$string = preg_replace("/[^a-zA-Z0-9 -]/", "", $string);
	$string = trim( strtolower( $string ) );
	$string = preg_replace("/\s/", $space, $string);
	$string = preg_replace("/".$space."+/", $space, $string);

	return $string;
}

$locales = array('fr_FR', 'fr_FR.utf8');
putenv('LANG='.implode(',', $locales));
setlocale(LC_ALL, $locales);
bindtextdomain(NAME, LIBPATH . DIRECTORY_SEPARATOR . 'i18n');
bind_textdomain_codeset(NAME, 'UTF8');
textdomain(NAME);

?>