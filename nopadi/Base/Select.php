<?php

namespace Nopadi\Base;

class Select
{
	private $query;

	public function __construct($select = '*',$calc=null)
	{
	  $select = is_array($select) ? implode(',',$select) : $select;
	  $select = str_ireplace('|',' AS ',$select);
	  $this->query = $select;
	}
	
	public function results()
	{
		return $this->query;
	}
}
