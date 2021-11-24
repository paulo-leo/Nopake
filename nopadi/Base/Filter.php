<?php

namespace Nopadi\Base;

class Filter
{

    private $filter;
	
	public function setFilter($filter)
	{
         
		$filter = str_ireplace('|||',' OR ',$filter);
		$filter = str_ireplace('||',' OR ',$filter);
		$filter = str_ireplace('!=',' != ',$filter);
		$filter = str_ireplace('>=',' >= ',$filter);
		$filter = str_ireplace('<=',' <= ',$filter);
		$filter = str_ireplace('>',' > ',$filter);
		$filter = str_ireplace('<',' < ',$filter);
		$filter = str_ireplace('[{',' LIKE \'%',$filter);
		$filter = str_ireplace('[}',' LIKE \'',$filter);
	    $filter = str_ireplace('}]','%\'',$filter);
		$filter = str_ireplace('{','\'',$filter);
		$filter = str_ireplace('}','\'',$filter);
		$filter = str_ireplace('|',' AND ',$filter);
		$filter = str_ireplace('=',' = ',$filter);
		$filter = str_ireplace(':',' BETWEEN ',$filter);
		$filter = str_ireplace('!',' IS NOT NULL ',$filter);
		$filter = str_ireplace('?',' IS NULL ',$filter);
		
		
		return $filter;
		
		 
	}
	
	public function getFilter($filter)
	{

	}
}
