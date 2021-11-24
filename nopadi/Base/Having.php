<?php

namespace Nopadi\Base;

class Having
{
	private $having;
	private $groupBy;
	private $orderBy;
	private $limit;

	public function having($select = null)
	{
		$select = !is_null($select) ? ' HAVING ' . $select : null;
		$this->having = $select;
	}
	public function orderBy($select = null)
	{
		$select = !is_null($select) ? ' ORDER BY ' . $select : null;
		$this->orderBy = $select;
	}
	public function groupBy($select = null)
	{
		$select = !is_null($select) ? ' GROUP BY ' . $select : null;
		$this->groupBy = $select;
	}
	public function limit($limit = null, $offset = null)
	{
		if (!is_null($limit) && is_int($limit) && is_null($offset)) {
			$limit = ' LIMIT ' . $limit;
		}
		if (!is_null($limit) && is_int($limit) && !is_null($offset) && is_int($offset)) {
			$limit = ' LIMIT ' . $limit . ',' . $offset;
		}
		$this->limit = $limit;
	}

	public function getHaving()
	{
		return $this->having;
	}
	public function getOrderBy()
	{
		return $this->orderBy;
	}
	public function getGroupBy()
	{
		return $this->groupBy;
	}
	public function getLimit()
	{
		return $this->limit;
	}
}
