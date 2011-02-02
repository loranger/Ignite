<?php

class DataCollection extends DataObject
{

	private $class;
	private $collection = array();
	private $where = array();
	private $constraint = array();

	function __construct( $table, $class, $where = false, $constraint = false )
	{
		$this->table = $table;
		$this->class = $class;
		if ( $where ) {
			$this->addWhere( $where );
		}
		if ( $constraint ) {
			$this->addConstraint( $constraint );
		}
	}

	function addWhere( $array )
	{
		$this->where = array_merge($this->where, $array);
	}

	function addConstraint( $array )
	{
		$this->constraint = array_merge($this->constraint, $array);
	}

	function get( $force = false )
	{
		if( !count($this->collection) || !$force )
		{
			$this->collection = false;

			$q = new DataQuery();
			$q->select('*')
				->from($this->table);

			if( count($this->where) )
			{
				$wheres = array();
				foreach ($this->where as $key => $value) {
					$wheres[] = $q->eq($key, $value);
				}
				$q->where( call_user_func_array( array($q, 'andx'), $wheres) );
			}

			foreach ($this->constraint as $key => $value) {
				switch( $key )
				{
					case 'order by':
						$q->orderBy($value);
						break;
					case 'limit':
						$arr = explode(',', $value);
						$limit = $arr[0];
						$offset = (isset($arr[1])) ? $arr[1] : false;
						$q->limit($limit, $offset);
						break;
				}
			}
			/*
				->where($q->orx(
						$q->eq('u.id', '?1'),
						$q->like('u.nickname', '?2')
					)
				)
				->where($q->eq('u.name', ':name'))
				->orderBy('u.surname', 'ASC')
				->groupBy('u.name');

			echo $q->build();
			echo $q->build()."\n<br/>";
			*/

			$st = DB()->prepare( $q->build() );

			if( $st->execute() )
			{
				$st->setFetchMode(PDO::FETCH_CLASS, $this->class);
				$this->collection = $st->fetchAll();
			}
		}
		return $this->collection;
	}
}


?>