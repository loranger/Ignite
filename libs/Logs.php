<?php

class Logs
{

	private $logs = array();

	function __construct()
	{

	}

	function log()
	{
		$args = func_get_args();
		$msg = $args[0];
		unset($args[0]);

		if( func_num_args() > 1 )
		{
			$msg = vsprintf($msg, $args);
		}

		error_log($msg, E_USER_WARNING);
	}

	function add($datas)
	{
		if( is_array($datas) )
		{
			foreach ($datas as $value)
			{
				$this->add($value);
			}
		}
		else
		{
			$backtrace = debug_backtrace();
			$backtrace = array_slice($backtrace, 2); // Remove internal calls

			$item = new LogItem();
			$item->value = $datas;
			$item->backtrace = $backtrace;

			array_push($this->logs, $item);
		}
	}

	function get()
	{
		return $this->logs;
	}

}

class LogItem extends stdClass
{
}

//##### Singleton shortcut function #####
function Logs()
{
	static $logs;
	if ( !$logs )
	{
		$logs = new Logs();
	}
	return $logs;
}

?>