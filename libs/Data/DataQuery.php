<?php

class DataQuery
{

	private $core = array();
	private $values = array();
	private $order = array();
	private $limit = array();

	function build()
	{
		$query = array();
		foreach ($this->core as $part => $value) {
			array_push( $query, sprintf( '%s %s',
											strtoupper($part),
											implode(', ', $value) ) );
		}
		if ( count($this->values) ) {
			array_push( $query, sprintf( '(%s) VALUES (%s)',
											implode(', ', array_keys( $this->values ) ),
											implode(', ', $this->values ) ) );
		}
		foreach ($this->order as $column => $sort) {
			array_push( $query, sprintf( 'ORDER BY %s %s',
											$column,
											$sort ) );
		}
		foreach ($this->limit as $limit) {
			array_push( $query, sprintf( 'LIMIT %s',
											$limit ) );
		}
		return sprintf('%s;', implode(' ', $query) );
	}

	private function add($target, $value)
	{
		if( !array_key_exists($target, $this->core) )
		{
			$this->core[$target] = array();
		}

		if( is_array($value) )
		{
			array_walk($value, array($this, $target));
		}
		else
		{
			$value = explode(',', $value);
			foreach ($value as $item) {
				array_push( $this->core[$target], $item );
			}
		}
		return $this;
	}

	private function addToLast($target, $value)
	{
		$this->core[$target][count($this->core[$target]) -1] .= $value;
		return $this;
	}

	function insert($table)
	{
		return $this->add('insert into', $table);
	}

	function select($select = null)
	{
		return $this->add('select', $select);
	}

	function delete($delete = null)
	{
		return $this->add('delete', $delete);
	}

	function update($table)
	{
		return $this->add('update', $table);
	}

	function value($column, $value)
	{
		$this->values[trim($column)] = $value;
		return $this;
	}

	function set($column, $value)
	{
		return $this->add('set', $this->eq($column, $value));
	}

	function from($from, $alias = false)
	{
		if ( $alias )
		{
			$from = sprintf('%s AS %s', $from, $alias);
		}
		return $this->add('from', $from);
	}

	function leftJoin($from, $condition = false)
	{
		$join = sprintf(' LEFT JOIN %s', $from);
		if( $condition )
		{
			$join = sprintf('%s ON %s', $join, $condition);
		}
		return $this->addToLast('from', $join);
	}

	function innerJoin($from, $condition = false)
	{
		$join = sprintf(' INNER JOIN %s', $from);
		if( $condition )
		{
			$join = sprintf('%s ON %s', $join, $condition);
		}
		return $this->addToLast('from', $join);
	}

	function where($where)
	{
		return $this->add('where', $where);
	}

	function orderBy($sort = null, $order = null)
	{
		$this->order[$sort] = $order;
		return $this;
	}

	function groupBy($groupBy)
	{
		return $this->add('group by', $groupBy);
	}

	function limit($limit, $offset)
	{
		$this->limit[0] = sprintf('%d, %d', $limit, $offset);
		return $this;
	}


	/** Condition **/

	function andx($x = null)
	{
		$args = func_get_args();
		return implode(' AND ', $args);
	}

	function orx($x = null)
	{
		$args = func_get_args();
		return implode(' OR ', $args);
	}


	/** Comparison **/

	function eq($x, $y)
	{
		return sprintf('%s = %s', $x, $y);
	}

	function neq($x, $y)
	{
		return sprintf('%s != %s', $x, $y);
	}

	function lt($x, $y)
	{
		return sprintf('%s < %s', $x, $y);
	}

	function lte($x, $y)
	{
		return sprintf('%s <= %s', $x, $y);
	}

	function gt($x, $y)
	{
		return sprintf('%s > %s', $x, $y);
	}

	function gte($x, $y)
	{
		return sprintf('%s >= %s', $x, $y);
	}


	/** Arithmetic **/

	function prod($x, $y) // x * y
	{
		return sprintf('%s * %s', $x, $y);
	}

	function diff($x, $y) // x - y
	{
		return sprintf('%s - %s', $x, $y);
	}

	function sum($x, $y) // x + y
	{
		return sprintf('%s + %s', $x, $y);
	}

	function quot($x, $y) // x / y
	{
		return sprintf('%s / %s', $x, $y);
	}


	/** Pseudo-function **/

	function is($return)
	{
		return sprintf('IS %s', $restriction);
	}

	function not($restriction)
	{
		return sprintf('IS NOT %s', $restriction);
	}

	function isNull($restriction)
	{
		return sprintf('%s ISNULL', $restriction);
	}

	function notNull($restriction)
	{
		return sprintf('%s NOTNULL', $restriction);
	}

	function in($x, $y)
	{
		return sprintf('%s IN (%s)', $x, implode(', ', $y) );
	}

	function notIn($x, $y)
	{
		return sprintf('%s NOT IN (%s)', $x, implode(', ', $y) );
	}

	function like($x, $y)
	{
		return sprintf('%s LIKE %s', $x, $y);
	}

	function between($val, $x, $y)
	{
		return sprintf('%s BETWEEN %s AND %s', $val, $x, $y );
	}

}

?>