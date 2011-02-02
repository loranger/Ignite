<?php

class Debug
{

	function get()
	{
		$out = tao('<div id="debug" />');
		Page()->addContent($out);
		$out = tao('<fieldset />')->addContent( tao('<legend>Debug</legend>') )->addTo($out);

		/*
		$out->addContent(tao('<strong />')->addContent('Session : '))->addContent(tao('<br />'));
		foreach ($_SESSION as $key => $val) {
			$out->addContent($key . ' : ' . $val)->addContent(tao('<br />'));
		}
		$out->addContent(tao('<br />'));
		*/

		/*
		$out->addContent(tao('<strong />')->addContent('User : '))->addContent(tao('<br />'));
		foreach (User() as $key => $val) {
			$out->addContent($key . ' : ' . $val)->addContent(tao('<br />'));
		}
		$out->addContent(tao('<br />'));
		*/

		$out->addContent(tao('<strong />')->addContent('Queries history : '))->addContent(tao('<br />'));
		foreach (Logs()->get() as $log) {
			$out->addContent($log->value)->addContent(tao('<br />'));
		}
	}

	function log()
	{
		$args = func_get_args();
		$trace = debug_backtrace();
		if( count( $args ) > 1 )
		{
			$pattern = $args[0];
			array_shift($args);
			$text = $trace[( (array_key_exists(1, $args)) ? 1 : 0)]['function'] . ' : ' . vsprintf($pattern, $args);
		}
		else
		{
			$text = $args[0];
		}
		syslog(LOG_WARNING, $text);
	}

}

//##### Singleton shortcut function #####
function Debug()
{
	static $dbg;
	if ( !$dbg )
	{
		$dbg = new Debug();
	}
	return $dbg;
}

?>