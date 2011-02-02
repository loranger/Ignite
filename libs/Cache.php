<?php

class Cache
{
	private $key;

	function __construct( $key )
	{
		$this->key = trim( $key );
	}

	function store( $value )
	{
		apc_store($this->key, $value);
		return $this;
	}

	function get()
	{
		return apc_fetch($this->key);
	}

	function delete()
	{
		return apc_delete($this->key);
	}
}

?>