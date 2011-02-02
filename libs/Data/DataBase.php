<?php

class DataBase extends PDO
{

	private $log = true;

	function __construct( $dsn )
	{
		try
		{
			$args = func_get_args();
			call_user_func_array( 'parent::__construct', $args);
		}
		catch(PDOException $e)
		{
			throw new Exception('<b>'.$e->getMessage().'</b>');
		}
	}

	private function logQuery($query_string)
	{
		return Logs()->add($query_string);
	}

	private function run($method, $args)
	{
		if( empty($args[0]) )
		{
			return;
		}
		if( $this->log )
		{
			$this->logQuery($args[0]);
		}
		$statement = call_user_func_array(array('parent', $method), $args);
		if ($statement === false) {
			$err = $this->errorInfo();
			throw new Exception(get_parent_class($this).': <b>'.$err[2].'</b>');
		}
		return $statement;
	}

	function query()
	{
		$args = func_get_args();
		return $this->run(__FUNCTION__, $args);
	}

	function exec( $strict_compliance )
	{
		$args = func_get_args();
		return $this->run(__FUNCTION__, $args);
	}

	function execute()
	{
		$args = func_get_args();
		return $this->run(__FUNCTION__, $args);
	}

	function prepare( $dummy_statement, $dummy_array = false )
	{
		$args = func_get_args();
		return $this->run(__FUNCTION__, $args);
	}

	function import( $file )
	{
		$import = new DataImport( $file );
	}

}

//##### Singleton shortcut function #####
function DB()
{
	static $db;
	if ( !$db )
	{
		$reflectionObj = new ReflectionClass('DataBase');
		$db = $reflectionObj->newInstanceArgs( explode(',', PDO_DSN) );
	}
	return $db;
}

?>