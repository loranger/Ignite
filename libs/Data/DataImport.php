<?php

class DataImport extends DataSource
{

	function __construct( $filename )
	{
		$this->loadSourceFromFile(SCHEMAPATH);
		$this->importFromObject( $this->source );
	}

	function importFromObject( $object )
	{
		$queries = array();

		if( property_exists($object, 'tables') )
		{
			foreach ($object->tables as $table => $fields) {
				$declarations = array();
				foreach ($fields as $id => $properties) {
					$declarations[] = sprintf('%s %s', $id, $properties);
				}
				$query = sprintf('CREATE TABLE %s (%s);', $table, implode(",\n", $declarations));
				array_push($queries, $query);
			}
		}

		if( property_exists($object, 'queries') )
		{
			foreach( $object->queries as $type => $lines )
			{

				foreach( $lines as $query )
				{
					if( preg_match("/values \((.+)\)/i", $query, $values) > 0 )
					{
						eval( sprintf('$array = array(%s);', $values[1]) );

						$array = array_map('trim', $array);

						foreach ($array as &$item) {
							$item = DB()->quote($item);
						}
						$query = preg_replace("/\((.+)\)/i", '('.implode(', ', $array).')', $query);
					}
					array_push($queries, $query);
				}
			}
		}
		array_map( array(DB(), 'exec'), $queries );
	}

}

?>