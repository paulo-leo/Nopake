<?php

namespace Nopadi\Base;

class Where
{
	private $query = null;
	private $where = null;
	private $whereOr = null;
	private $whereIn = null;
	private $whereNotIn = null;
	private $whereNull = null;
	private $whereNotNull = null;

	function setWhere($filter, $op = null, $val = null, $val2 = null, $type = 'AND')
	{

		if (is_string($filter)) {
			$filter = [
				[$filter, $op, $val, $val2]
			];
		}
		foreach ($filter as $key => $val) {

			$k = $val[0];
			$o = isset($val[1]) ? $val[1] : null;
			$v = isset($val[2]) ? $val[2] : null;
			$v2 = isset($val[3]) ? $val[3] : null;

			if (is_null($v) && $o != 'null' && $o != '!null') {
				$v = $o;
				$o = '=';
			}

			if ($type == 'AND') {
				$this->where[] = array('key' => $k, 'val' => $v, 'op' => $o, 'val2' => $v2);
			} else {
				$this->whereOr[] = array('key' => $k, 'val' => $v, 'op' => $o, 'val2' => $v2);
			}
		}
	}
	function getWhere($type = 'AND')
	{
		$where = null;
		$cond = ($type == 'AND') ? $this->where : $this->whereOr;
		$count = count($cond);
		if ($count > 0) {
			foreach ($cond as $id) {
				extract($id);
				if (is_string($val)) $val = "'{$val}'";
				if (is_string($val2)) $val2 = "'{$val2}'";
				if ($count == 1) {
					if ($op == 'null') {
						$where = $key . ' IS NULL';
					} elseif ($op == '!null') {
						$where = $key . ' IS NOT NULL';
					} elseif ($op == 'in') {
						$where = $key . ' ' . $this->getIn($val,'IN');
					} elseif ($op == '!in') {
						$where = $key . ' ' . $this->getIn($val, 'NOT IN');
					} elseif ($op == 'bet' || $op == 'between') {
						$where = $key . ' BETWEEN ' . $val . ' AND ' . $val2;
					} else {
			            $val = str_ireplace('\'','',$val);
						if($op == 'like')
						{
							$where = "{$key} LIKE '%{$val}%'";
						}elseif($op == '.like')
						{
							$where = "{$key} LIKE '%{$val}'";
						}
						elseif($op == 'like.')
						{
							$where = "{$key} LIKE '{$val}%'";
						}elseif($op == '.like.')
						{
							$where = "{$key} LIKE '{$val}'";
						}
						else{
							$val = is_numeric($val) ? $val : "'{$val}'";
							$where = $key . ' ' . $op . ' ' . $val;
						}  
						
					}
				} else {
					if ($op == 'null') {
						$where .= ' ' . $key . ' IS NULL ' . $type . ' ';
					} elseif ($op == '!null') {
						$where .= ' ' . $key . ' IS NOT NULL AND ' . $type . ' ';
					} elseif ($op == 'in') {
						$where .= ' ' . $key . ' ' . $this->getIn($val) . ' ' . $type . ' ';
					} elseif ($op == '!in') {
						$where .= $key . ' ' . $this->getIn($val, 'NOT IN') . ' ' . $type . ' ';
					} elseif ($op == 'bet' || $op == 'between') {
						$where .= ' ' . $key . ' BETWEEN ' . $val . ' AND ' . $val2 . ' ' . $type . ' ';
					} else {
						$val = str_ireplace('\'','',$val);
						if($op == 'like')
						{
							$where = "{$key} LIKE '%{$val}%' {$type} ";
						}elseif($op == '.like')
						{
							$where .= "{$key} LIKE '%{$val}' {$type} ";
						}
						elseif($op == 'like.')
						{
							$where .= "{$key} LIKE '{$val}%' {$type} ";
						}elseif($op == '.like.')
						{
							$where .= "{$key} LIKE '{$val}' {$type} ";
						}
						else{
							$val = is_numeric($val) ? $val : "'{$val}'";
							$where .= $key . ' ' . $op . ' ' . $val . ' ' . $type . ' ';
						}  
					}
				}
			}
			if ($count > 1) {
				if ($type == 'AND') $where = trim(substr($where, 0, -4));
				else $where = trim(substr($where, 0, -3));
			}
		}

		return $where;
	}


	function getWhereOr()
	{
		$where = null;
		$cond = $this->whereOr;
		$count = count($cond);
		if ($count > 0) {
			foreach ($this->whereOr as $id) {
				extract($id);
				if (is_string($val)) $val = "'{$val}'";
				if ($count == 1) {
					$where = $key . ' ' . $op . ' ' . $val;
				} else {
					$where .= $key . ' ' . $op . ' ' . $val . ' OR ';
				}
			}
			if ($count > 1) $where = trim(substr($where, 0, -3));
		}

		return $where;
	}

	private function getIn($val, $type = 'IN')
	{
		$in = null;
		$in .= $type . ' (';
		for ($i = 0; $i < count($val); $i++) {
			if (is_string($val[$i])) $val[$i] = "'{$val[$i]}'";
			$in .= $val[$i] . ',';
		}
		$in = trim(substr($in, 0, -1));
		$in .= ') ';
		return $in;
	}

	function getWhereNotIn()
	{
		$in = null;
		foreach ($this->whereNotIn as $key => $val) {
			$in .= $key . 'NOT IN (';
			for ($i = 0; $i < count($val); $i++) {
				if (is_string($val[$i])) $val[$i] = "'{$val[$i]}'";
				$in .= $val[$i] . ',';
			}
			$in = trim(substr($in, 0, -1));
			$in .= ') AND ';
		}
		$in = trim(substr($in, 0, -4));
		return $in;
	}


	public function results()
	{
		$this->query = $this->getWhere() . $this->getWhereOr();
		return $this->query;
	}
}
