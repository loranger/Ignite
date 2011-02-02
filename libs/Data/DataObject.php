<?php

class DataObject extends DataSource
{

	protected $table;
	protected $collections;
	protected $objects = array();

	function __construct( $id = false )
	{
		if( $id && isset( $this->table ) )
		{
			$this->loadFromId($id);
		}
	}

	function getId()
	{
		return ( isset($this->id) ? $this->id : false );
	}

	function loadFromId($id)
	{
		return $this->loadFromField('id', $id);
	}

	function loadFromField($field, $value)
	{
		$q = new DataQuery();
		$q->select('*')->from($this->table)->where($q->eq($field, ':value'));
		$st = DB()->prepare( $q->build() );
		$st->bindParam(':value', $value);
		if( $st->execute() )
		{
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$result = $st->fetch();
			return $result;
		}
		return false;
	}

	function getObjectFromId($class_name, $id)
	{
		if( !array_key_exists( $class_name, $this->objects ) )
		{
			$this->objects[$class_name] = array();
		}
		if( !array_key_exists( $id, $this->objects[$class_name] ) )
		{
			$this->objects[$class_name][$id] = new $class_name($id);
		}
		return $this->objects[$class_name][$id];
	}

	function getObjectsCollectionFromTable($class_name, $table_name, $where = false, $constraint = false)
	{
		if( !isset( $this->collections[$table_name.$class_name] ) )
		{
			$collection = new DataCollection( $table_name, $class_name, $where, $constraint );
			$this->collections[$table_name.$class_name] = $collection->get();
		}
		return $this->collections[$table_name.$class_name];
	}

	function create()
	{
		if( !$this->getId() )
		{
			$args = func_get_args();
			if( count( $args ) == 1 && is_array( $args[0] ) )
			{
				$array = $args[0];
			}
			else if ( count($args) == 2 )
			{
				$array = array( $args[0] => $args[1] );
			}

			$q = new DataQuery();
			$params = array();

			$columns = array_flip($this->getColumnNames());
			unset($columns['id']);
			$array = array_intersect_key( $array, $columns );

			if ( count($array) ) {
				$q->insert($this->table);
				foreach ($array as $column => $value) {
					$param_name = sprintf(':%s', $column);
					$q->value($column, $param_name);
					$params[$param_name] = $value;
				}

				$st = DB()->prepare( $q->build() );
				if( $st->execute( $params ) )
				{
					foreach ($array as $column => $value) {
						$this->$column = $value;
					}
					$this->id = DB()->lastInsertId();
					return $this;
				}
			}
		}
		return false;
	}

	function update()
	{
		$args = func_get_args();
		if ( count($args) == 0 )
		{
			return $this->updateFields( get_object_vars( $this ) );
		}
		else if( count( $args ) == 1 && is_array( $args[0] ) )
		{
			return $this->updateFields( $args[0] );
		}
		else if ( count($args) == 2 )
		{
			return $this->updateField( $args[0], $args[1] );
		}
	}

	function updateField($column, $value)
	{
		return $this->updateFields( array($column => $value) );
	}

	function updateFields($array)
	{
		$q = new DataQuery();
		$params = array();

		$columns = array_flip($this->getColumnNames());
		unset($columns['id']);
		$array = array_intersect_key( $array, $columns );

		if ( count($array) ) {
			$q->update($this->table);
			foreach ($array as $column => $value) {
				$param_name = sprintf(':%s', $column);
				$q->set($column, $param_name);
				$params[$param_name] = $value;
			}

			$q->where($q->eq('id', ':id'));
			$params[':id'] = $this->getId();

			$st = DB()->prepare( $q->build() );
			if( $st->execute( $params ) )
			{
				foreach ($array as $column => $value) {
					$this->$column = $value;
				}
				return $this;
			}
		}

		return false;
	}

	function delete()
	{
		$q = new DataQuery();
		$q->delete()->from($this->table)->where($q->eq('id', ':id'));

		$st = DB()->prepare( $q->build() );
		$st->bindParam(':id', $this->getId());
		if( $st->execute() )
		{
			return true;
		}
		return false;
	}

}

?>