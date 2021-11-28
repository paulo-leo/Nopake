<?php

namespace Nopadi\Base;
use PDO;
use Nopadi\Base\DB;

class Schema
{
	private $table_name;
	private $column;
	private $modify;
	private $querys;
	private $end;

	/*Cria uma tabela*/
	public function createTable($table_name){
		$this->table_name = $table_name;
	}

	public function __construct($table=null)
	{
		$this->table_name = $table;
	}
	
	/*Cria uma coluna*/
	public function addColumn($name,$type,$args=null){
		
	    $size = isset($args['size']) ? $args['size'] : null;
		/*Nome da tabela para referencia de chave estrangeira PK*/
		$table = isset($args['table']) ? $args['table'] : null;
		/*Nome da coluna para referencia de chave estrangeira FK*/
		$ref = isset($args['table_key']) ? $args['table_key'] : 'id';
		/*define um valor default*/
		$def = isset($args['default']) ? $args['default'] : null;
		$add = isset($args['add']) ? $args['add'] : null;
		
		$type = strtoupper($type);
		
		switch($type){
			
			/*Cria uma campo de chave primaria*/
			 case 'PK' : 
		         $this->column .= $name . ' BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY(' . $name . ')';
             break;
			 
			 /*Cria uma campo de chave estrangeira*/
			 case 'FK' :  
			 $column = trim($name . ' BIGINT(20) UNSIGNED NOT NULL, FOREIGN KEY(' . $name . ') REFERENCES ' . $table . '(' . $ref . ')');
		      $this->column .= $column;
			  break;
			  
			  case 'LT':
			   /*Cria uma campo do tipovarchar fixo*/
			  $column = trim($name .' LONGTEXT '.$this->def($def));
		      $this->column .= $column;
			  break;
			  
			   default :
			   
			   if($type == 'CHAR' && $size > 250) $size = 250; 
			   if($type == 'VARCHAR' && $size > 555) $size = 555;
				   
			   $size = !is_null($size) ? "($size)" : null;
			   
			  $column = trim($name .' '.$type.$size.' '.$this->def($def));
		      $this->column .= $column;
		
			  
		}
		
	     $this->column .= ',';
		
	}
	
	/*Cria um campo do tipo string*/
	public function text($name, $size=250, $dafault=null){
		$this->addColumn($name,'varchar',['size'=>$size,'dafault'=>$dafault]);
	}
	
	/*Cria um campo do tipo string fixa*/
	public function fixed($name, $size=250, $dafault=null){
		$this->addColumn($name,'char',['size'=>$size,'dafault'=>$dafault]);
	}
		
	/*Cria um campo do tipo string longa*/
	public function longText($name, $dafault=null){
		$this->addColumn($name,'LT',['dafault'=>$dafault]);
	}
	
	/*Cria um campo do tipo integer*/
	public function int($name, $size=10, $dafault=null){
		 $arr = ['size'=>$size,'dafault'=>$dafault];
		$this->addColumn($name,'int',$arr);
	}
	
	public function user_id()
	{
		$this->int('user_id', 11);
	}
	
	/*Cria um campo do tipo float para lidar com valores monetários*/
	public function money($name, $dafault=null){
		$column = trim($name .' FLOAT(15,2) '.$this->def($dafault) . ',');
		$this->column .= $column;
	}
	
	/*Cria um campo do tipo float*/
	public function float($name, $size="6,2", $dafault=null){
		$column = trim($name .' FLOAT('.$size.') '.$this->def($dafault) . ',');
		$this->column .= $column;
	}

	/*Permite escrever um comando livre para definir uma coluna*/
	public function your($sql){
		$this->column .= trim($sql.',');
	}
	
	/*Permite criar uma coluna do tipo detetime*/
	public function datetime($name,$null=true){
		$null = $null ? 'DEFAULT NULL':'NOT NULL DEFAULT'; 
		$this->column .= trim($name.' DATETIME '.$null.',');
	}
	
	public function created_at()
	{
		$this->timestamp('created_at');
	}
	
	/*Permite criar uma coluna do tipo time*/
	public function time($name,$null=true){
		$null = $null ? 'DEFAULT NULL':'NOT NULL DEFAULT'; 
		$this->column .= trim($name.' TIME '.$null.',');
	}
	/*Cria uma coluna do tipo data e hora automática*/
	public function timestamp($name){ 
		$this->column .= trim($name.' TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,');
	}

	/*Cria um campo do tipo primary key*/
	public function pk($name="id"){
		$this->addColumn($name,'pk');
	}
	
	/*Cria um campo do tipo foreing key*/
	public function fk($name,$table,$ref=null){
		$ref = is_null($ref) ? 'id':$ref;
		$this->addColumn($name,'fk',['table'=>$table,'table_key'=>$ref]);
	}
	
	/*Alterar o tipo de dados de uma coluna*/
	public function modifyCol($columnName,$typeOfData){
		
	   $this->modify .= "ALTER TABLE ".$this->table_name." MODIFY COLUMN ".$columnName." ".$typeOfData.";";
		
	}
	
	/*Excluir uma coluna de uma tabela*/
	public function dropCol($columnName){
		
	   $this->modify .= "ALTER TABLE ".$this->table_name." DROP COLUMN ".$columnName.";";
		
	}
	
	/*Coloca o campo de chave primária de uma tabela em conformidade*/
	public function defPK($table,$id="id"){
		
	   $this->modify .= "ALTER TABLE ".$table." CHANGE ".$id." ".$id." BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;";
		
	}
	
	/*Adicionar uma coluna à tabela*/
	public function addCol($columnName){
		
	   $this->modify .= "ALTER TABLE ".$this->table_name." ADD COLUMN ".$columnName.";";
		
	}
	
	/*Exlui uma tabela*/
	public function dropTable($tables)
	{
		$tables = is_array($tables) ? $tables : array(0=>$tables);
        foreach($tables as $table)
		{
		  $this->modify .= "DROP TABLE IF EXISTS ".$table.";";
		}
	  
	    $this->end .= $this->modify;	
	}
	
    /*Retonar o valor default do campo*/
	private function def($def = null)
	{
		if (!is_null($def)) {
			if (is_bool($def) && $def == true) {
				$def = ' NOT NULL';
			} else {
				$def = " NOT NULL DEFAULT '{$def}'";
			}
		}
		return $def;
	}

    /*Executa o esquema*/
	public function execute()
	{
		$schema = null;
		
		
        if($this->column)
		{
				$schema .= 'CREATE TABLE IF NOT EXISTS ' . $this->table_name;
		        $schema .= '(' . substr($this->column, 0, -1) . ') ENGINE = innodb DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
		
		}
		
		if($this->modify){
			$schema .= $this->modify;
		}
		
		$this->end	.= $schema;	
		return DB::executeSql($this->end);
	}
}
