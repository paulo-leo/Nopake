<?php

namespace Nopadi\Base;

use PDO;
use Nopadi\Base\DB;

class Table
{
	 private $prefix = null;
	 /*cria um prefixo de tabelas*/
	 public function prefix($name)
	 {
		$this->prefix = $name.'_';
	 }
	/*Cria uma ou mais tabelas*/
    public function create($tables)
	{  
		$sql = null;
        foreach($tables as $table_name=>$table_cols)
		{
			$table_name = $this->prefix.$table_name;
			$sql .= "CREATE TABLE IF NOT EXISTS {$table_name}(";
			foreach($table_cols as $table_col)
			{
				$table_col = explode('|',$table_col);
				$col = explode(':',$table_col[0]);
				$col = $col[0];
				$type = $this->getType($table_col);
				$sql .= "{$col} {$type},";
			}
			$sql = substr($sql,0,-1);
			$sql .= ") ENGINE = innodb DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
		}
		return DB::executeSql($sql);
	}
	/*Elimina uma ou mais tabelas*/
    public function drop($tables)
	{
		$tables = is_string($tables) ? array($tables) : $tables;
		$sql = null;
        foreach($tables as $table)
		{   $table = $this->prefix.$table;
			$sql .= "DROP TABLE IF EXISTS {$table};";
		}
		return DB::executeSql($sql);
	}
	/*Retorna o valor pelo tipo de campo especificado*/
	private function getValue($key,$values,$def=null)
	{  
	   $r = null;
       foreach($values as $value)
	   {
		$value = explode(':',$value);
		$value1 = $value[0];
		$value2 = isset($value[1]) ? $value[1] : null;
		if($key == $value1){
           $r = is_null($def) ? $value2 : $def;
		   break;
		 }
	   } 
	   return $r;
	}
   /*Retorna o tipo de dado com os seus valores padrões*/
    private function getType($array)
	{
		$v = null;
		$names = explode(':',$array[0]);
		$name = $names[0];
		$type = $array[1];
        $type = str_ireplace('string','varchar',$type);
		$type = str_ireplace('number','int',$type);

		$null =  $this->getValue('null',$array,'NULL');
		$null = $null ? $null : 'NOT NULL';

		$default = $this->getValue('default',$array);
		$default = $default ? "DEFAULT '{$default}'" : null;

		$dates = array('date','time','datetime');
		
		if(in_array($type,$dates)){
           $r = "{$type} {$null} {$default}";
		}elseif($type == 'timestamp'){
			$r = "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
		}elseif($type == 'primary_key' || $type == 'pk'){
			$r = "BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY({$name})";
		}elseif($type == 'foreign_key' || $type == 'fk'){
			$table = $names[1];
			$ref = $names[2];
			$r = "BIGINT(20) UNSIGNED NOT NULL, FOREIGN KEY({$name}) REFERENCES {$table}({$ref})";
		}elseif($type == 'money'){
			$default = !$default ? "DEFAULT '0'" : $default;
			$r = "FLOAT(15,2) {$null} {$default}";
			$r = trim(str_replace('  ',' ',$r));
		}else{
			$size = $this->getValue('size',$array);
			$size = ($type == 'varchar' && is_null($size)) ? 250 : $size;

			$size = $size ? $size : 11;
			$r =  "{$type}({$size}) {$null} {$default}";
			$r = trim(str_replace('  ',' ',$r));
			$r = strtoupper($r);
		}
		return $r;
	}
}