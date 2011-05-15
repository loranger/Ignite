<?php

/**
 * Toolbox
 * A pool of useful functions
 *
 * @author loranger
 */

/**
 * Return a post or get parameter value
 *
 * @param string $name of the requested value
 * @return mixed value or the post or get parameter, or false
 * @author loranger
 **/
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

/**
 * Detect if a post or get parameter exists
 *
 * @param string $name of the requested value
 * @return boolean
 * @author loranger
 **/
function hasParam($name)
{
	return ( getParam($name) !== false );
}

/**
 * Return a byte value from a string formated size
 *
 * @param string $value such as '1 G' or "200M"
 * @return integer value in bytes
 * @author loranger
 **/
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

/**
 * Build a unique slug string from a given one
 *
 * @param string $string to "sluggify""
 * @param string $space character used in slug
 * @return string
 * @author loranger
 **/
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

?>