<?php

class DataSource
{

	protected $source;
	private $columns = array();

	private function arrayToObject( $array )
	{
		$object = new stdClass();
		if ( is_array($array) && count($array) > 0 )
		{
			foreach ( $array as $name => $value )
			{
				if ( !is_numeric( $name ) )
				{
					$name = strtolower( trim($name) );
				}

				if ( $value )
				{
					$object->$name = is_array($value) ? $this->arrayToObject($value) : $value;
				}
			}
		}
		return $object;
	}

	protected function loadSourceFromFile( $filename )
	{
		if( !file_exists( $filename ) )
		{
			$msg = sprintf('<b>Unable to load %s : File not found</b>', $filename);
			throw new Exception($msg);
		}

		$ext = substr( strrchr( $filename, '.' ), 1 );
		switch( $ext )
		{
			case 'sql':
				$this->source = $this->loadSourceFromSql( $filename );
				break;
			case 'ini':
				$this->source = $this->loadSourceFromIni( $filename );
				break;
			case 'json':
				$this->source = $this->loadSourceFromJson( $filename );
				break;
			case 'yml':
			case 'yaml':
				$this->source = $this->loadSourceFromYml( $filename );
				break;
			default:
				$msg = sprintf('<b>Unable to load data : %s files not supported</b>', $ext);
				throw new Exception($msg);
				break;
		}
		return $this->source;
	}

	private function loadSourceFromSql($file)
	{
		$lines = explode(';', file_get_contents($file) );

		$array = array( 'tables' => array(), 'queries' => array( 'sql' => array() ) );

		foreach ($lines as $line)
		{
			if ( preg_match('/create table/i', $line) )
			{
				preg_match('/create table\s+(?<name>\w+)\s+\((?<declaration>[^)]+)/im', trim($line), $matches);
				$array['tables'][$matches['name']] = array();
				$declarations = array_map('trim', explode(',', $matches['declaration']) );
				foreach ($declarations as $declaration) {
					preg_match('/(?<field>\w+)(?<constraint>.+)/i', $declaration, $details);
					$array['tables'][$matches['name']][$details['field']] = trim( $details['constraint'] );
				}
			}
			else if ( trim($line) != '' )
			{
				array_push( $array['queries']['sql'], trim($line) );
			}
		}
		return $this->arrayToObject( $array );
	}

	private function loadSourceFromIni( $file )
	{
		$ini = parse_ini_file($file, true);
		return $this->arrayToObject( $ini );
	}

	private function loadSourceFromJson( $file )
	{
		return json_decode( file_get_contents($file) );
	}

	private function loadSourceFromYml($file)
	{
		$array = Spyc::YAMLLoad($file);
		return $this->arrayToObject( $array );
	}

	private function loadColumnsFromSource()
	{
		foreach ($this->source->tables->{$this->table} as $column_name => $column_declaration) {

			$replacement_pattern = '###';
			preg_match("/('([a-z ]+)')/", $column_declaration, $matches);
			if( count($matches) )
			{
				$pattern = sprintf('/%s/', $matches[0]);
				$column_declaration = preg_replace($pattern, $replacement_pattern, $column_declaration);
			}
			$declarations = explode(' ', $column_declaration);
			if( $key = array_search($replacement_pattern, $declarations) )
			{
				$declarations[$key] = $matches[2];
			}

			$column = new DataColumn($column_name);
			$column->setDeclarations( $declarations );
			$this->columns[$column_name] = $column;
		}
	}

	function getColumns()
	{
		if( !count( $this->columns ) )
		{
			$this->loadSourceFromFile(SCHEMAPATH);
			$this->loadColumnsFromSource();
		}
		return $this->columns;
	}

	function getColumnNames()
	{
		return array_keys( $this->getColumns() );
	}

	function getColumn($name)
	{
		if( array_key_exists( $name, $this->getColumns() ) )
		{
			return $this->columns[$name];
		}
		return false;
	}
}

?>