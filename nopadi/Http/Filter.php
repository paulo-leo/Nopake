<?php
/*
Autor: Paulo Leonardo da Silva Cassimiro
*/
namespace Nopadi\Http;

class Filter
{
    private $all;
	private $only = null;
	private $types = array();
	private $prefix = null;
	
  public function __construct()
  {
    switch ($_SERVER['REQUEST_METHOD']) 
	  {
           case 'GET':
				$this->all = isset($_GET) ? $_GET : array();
				break;
			case 'POST':
				$this->all = isset($_POST) ? $_POST : array();
				break;
			default:
				$_REQUEST_NOPADI = file_get_contents('php://input');
				parse_str($_REQUEST_NOPADI, $_REQUEST_NOPADI);
				$this->all = isset($_REQUEST_NOPADI) ? $_REQUEST_NOPADI : array();
				break;
	  }
   }
   
   /*Retona todos os valores*/
   private function all()
   {
	   $this->del(array(
	     '_token'
	   ));
	   
	  return $this->all; 
   }

   /*Define somente o conjunto de valores que serão usados*/
   public function only($list)
   {
	   $this->only = $list;
   }
   
   /*Faz um loop para montar o filtro SQL*/
   private function loop()
   {
	  $filters = $this->all(); 
	  $where = null;
	  foreach($filters as $key=>$val)
	  {   
	    /*$val = str_replace([
		  '%40',
		  '%3C',
		  '%3E',
		  '%3D',
		  '%3A',
		  '%7B',
		  '%7D',
		  '%7C'
		  ],[
		  '@',
		  '<',
		  '>',
		  '=',
		  ':',
		  '{',
		  '}',
		  '|'
		  ],$val);*/
		  
		  if(is_null($this->only)){
			  $where .= $this->igualIn($key,$val).' AND ';
		  }else{
			  if(in_array($key,$this->only))
			  {
				 $where .= $this->igualIn($key,$val).' AND '; 
			  }
		  }
	  }
	   
	  $where = trim($where);
	  $where = substr($where,0,-3);
	  $where = trim($where);
	  return $where;
	
   }
   
   /*Define um valor antes do filtro*/
   public function prefix($prefix)
   {
	  $this->prefix = $prefix;
   }
   
   /*Define um valor*/
   public function set($values)
   {
	 foreach($values as $key=>$val)
	 {
		$this->all[$key] = $val;
	 }  
   }
   
   /*Deleta um conjunto de valores*/
   public function del($key)
   {
	   if(is_array($key))
	   {
		  foreach($key as $keys)
		  {
			 unset($this->all[$keys]); 
		  }   
	   }else{
		   unset($this->all[$key]);
	   }
   }
 
   /*Obtem todo o filtro SQL*/
   public function gets($content_first=null)
   {
	   if(!is_null($content_first)){
		  $content_first = trim($content_first);
		  $content_first = strtoupper($content_first).' '; 
	   }
	   $mount = ' '.$content_first.$this->loop().' ';
	   return (trim($mount) == trim($content_first)) ? ' ' : $mount;
   }
   
   /*Define somente o conjunto de valores que serão usados*/
   public function types($list)
   {
	   $this->types = $list;
   }
   
   private function inType($key,$value)
   {
	   if(array_key_exists($key, $this->types)) 
	   {
           $type = $this->types[$key];
		   
		   if($type == 'int' || $type == 'integer')
		   {
			  $value = is_numeric($value) ? intval($value) : 0;
		   }
		   elseif($type == 'float' || $type == 'real')
		   {
			  $value = is_numeric($value) ? floatval($value) : 0;
		   }
      }
	  return $value;
   }
  
  /*Monta a conulta indivudualemnte*/
  private function igualIn($name,$value)
  {
	 $prefix = !is_null($this->prefix) ? $this->prefix.'.' : null;
	 $x = explode(',', $value); 
	 $total = count($x);
	 $values = null;
	 
	 if($total > 1){
		 
		 for($i=0;$i<$total;$i++)
		 {
			$index = is_numeric($x[$i]) ? $x[$i] : "`{$x[$i]}`";
			$values .= $index.',';
		 }
		 $values = substr($values,0,-1);
		 $values = $prefix.$name.' IN('.$values.')';
		 
	 }else{
		 
		 $op = substr($value,0,1);
		 $op2 = substr($value,0,2);
		 
		 if($op == '@'){
			 $value = substr($value,1);
			 $values = $prefix.$name.' LIKE '."'%{$value}%'";
		 }
		 elseif($op == '{'){
			 $value = substr($value,1);
			 $values = $prefix.$name.' LIKE '."'%{$value}'";
		 }
		 elseif($op == '}'){
			 $value = substr($value,1);
			 $values = $prefix.$name.' LIKE '."'{$value}%'";
		 }
		 elseif($op == '|'){
			 $value = substr($value,1);
			 $value =  explode('|',$value);
			 $d1 = $value[0];
			 $d2 = isset($value[1]) ? $value[1] : $d1;
			 $values = $prefix.$name.' BETWEEN '."'{$d1}' AND '{$d2}'";
		 }
		 elseif($op2 == '<>'){
			 $value = substr($value,2);
			 $value = is_numeric($value) ? $value : 0;
			 $values = $prefix.$name.' <> '.$value;
		 }
		  elseif($op2 == '>='){
			 $value = substr($value,2);
			 $value = is_numeric($value) ? $value : 0;
			 $values = $prefix.$name.' >= '.$value;
		 }
		 elseif($op2 == '<='){
			 $value = substr($value,2);
			 $value = is_numeric($value) ? $value : 0;
			 $values = $name.' <= '.$value;
		 }
		 elseif($op == '>'){
			 $value = substr($value,1);
			 $value = is_numeric($value) ? $value : 0;
			 $values = $prefix.$name.' > '.$value;
		 }
		 elseif($op == '<'){
			 $value = substr($value,1);
			 $value = is_numeric($value) ? $value : 0;
			 $values = $prefix.$name.' < '.$value;
		 }
		 else{
			 $value = $this->inType($name,$value);
			 $values = is_numeric($value) ? $value : "'{$value}'";
		     $values = $prefix.$name.' = '.$values;
		 }
	 }
	 return $values;
  }
}



