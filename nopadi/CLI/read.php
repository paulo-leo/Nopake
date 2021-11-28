<?php
/*
*
*Author: Paulo Leonardo da Silva Cassimiro
*
*/

class read
{
  private $arr = array();
  private $filename;
  
  /*Ler o caminho do arquivo*/
  public function __construct($file){
	  
	  
	  $this->filename = $file;

	  $file = @file_get_contents($this->filename);

	  if($file){
		 $file = json_decode($file,true);
         $this->arr = $file;		 
	  } 
  }
  
  /*Retona todos os valores*/
  public function gets()
  {
	return $this->arr;  
  }
  
  /*Retorna um valor por meio da chave especifica*/
  public function get($key)
  {
	  
	  if(isset($this->arr[$key]))
	        return $this->arr[$key];	
		
  }
  
  
  /*Adiciona um novo elemento ao nó de dados*/
  public function set($key,$val=null)
  {  
	  $this->arr[$key] = $val; 
  }
}