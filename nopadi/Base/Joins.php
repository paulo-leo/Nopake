<?php

namespace Nopadi\Base;

class Joins
{
	private $joins = null;

	function setJoin($table, $key1, $op, $key2=null, $type = 'INNER JOIN')
	{
		$op = trim($op);
		
		if(is_null($key2))
		{
		  $key2 = $op;
          $op = '=';		  
		}
		
		$this->joins = array(
			'table' => $table,
			'key1' => $key1,
			'key2' => $key2,
			'op' => $op,
			'type' => $type
		);
	}

	function getJoin()
	{
		$table = $this->joins['table'];
		$type = $this->joins['type'];
		$key1 = $this->joins['key1'];
		$key2 = $this->joins['key2'];
		$op = $this->joins['op'];

		$joins = $type . ' ' . $table . ' ON ' . $key1 . ' ' . $op . ' ' . $key2;

		return $this->joins = trim($joins);
	}
}
