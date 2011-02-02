<?php

class DataColumn
{
	private $name;
	private $type;
	private $null = true;
	private $constraint = array();
	private $default;

	function __construct($name)
	{
		$this->name = trim($name);
	}

	function setDeclarations( $declarations = array() )
	{
		$declarations = array_map('strtolower', $declarations);

		while ( $constraint = current($declarations) ) {
			if( $constraint != '' )
			{
				switch ( $constraint ) {
					case 'integer':
					case 'real':
					case 'text':
					case 'blob':
					case 'bool':
					case 'boolean':
						$this->type = $constraint;
						break;
					case 'not':
						break;
					case 'null':
						if( array_search( 'not', $declarations ) !== false )
						{
							$this->null = false;
							$this->setDeclaration( 'not null' );
						}
						else
						{
							$this->setDeclaration( $constraint );
						}
						break;
					case 'default':
						$this->default = next($declarations);
						break;
					default:
						$this->setDeclaration( $constraint );
						break;
				}
			}
			next($declarations);
		}
	}

	function setDeclaration( $constraint )
	{
		array_push( $this->constraint, $constraint );
	}

	function getName()
	{
		return $this->name;
	}

	function getType()
	{
		return $this->type;
	}

	function getDefault()
	{
		return $this->default;
	}

	function isNull()
	{
		return $this->null;
	}

	function isNotNull()
	{
		return !$this->null;
	}

}

?>