<?php
/*
*
*Author: Paulo Leonardo da Silva Cassimiro
*
*/

namespace Nopadi\FS;

use Nopadi\Http\URI;

class Json
{
  private $arr = array();
  private $filename;
  
  /*Ler o caminho do arquivo*/
  public function __construct($file,$local=true){
	  if($local){
	      $uri = new URI();
	      $file = $uri->local($file);
	  }
	  
	  $this->filename = $file;

	  $file = @file_get_contents($this->filename,true);

	  if($file){
		  $file = $this->revert_utf8($file);
		 //Remove os caracteres não imprimíveis da string
		 $file = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $file);

		 //Transforma a STRING no formato JSON em Array 
		 $file = json_decode($file,true);
         $this->arr = $file;		 
	  } 
  }
 
   /*checa se uma chave existe*/
   public function exists_key($key){
	   return array_key_exists($key, $this->gets());
   }
   
   /*Verifica quantos indices existem e retorna a sua quantidade*/
   public function has(){
	   $count = count($this->gets());
	   return  $count > 0 ? $count : false;
   }
 
  /*Retorna um valor por meio da chave especifica*/
  public function get($key,$key2=null)
  {
	  if($key2){
		 if(isset($this->arr[$key][$key2]))
	        return $this->revert_utf8($this->arr[$key][$key2],true); 
	  }else{
	  if(isset($this->arr[$key]))
	        return  $this->revert_utf8($this->arr[$key],true);	
	  }
  }
  
  /*Adiciona um novo elemento ao nó de dados*/
  public function set($key,$val=null,$val2=null)
  {  
      if($val2){
		  $this->arr[$key][$val] = $val2; 
	  }else{
		  $this->arr[$key] = $val; 
	  }
  }
  
  /*Retona todos os valores*/
  public function gets()
  {
	return $this->arr;  
  }
  
  public function val($key,$index=null,$default=null)
  { 
	  $key = $this->get($key);
	  if(is_array($key) && !is_null($index)){
		 $key = array_key_exists($index,$key) ? $key[$index] : $default;
	  }
	  return $key;
   }
  
  /*Retorna todos os valores em string no formato JSON*/
  public function read($format=false)
  {
	 if($format){
		return json_encode($this->arr,JSON_PRETTY_PRINT);
	 }else{
		return json_encode($this->arr); 
	 } 	
  }
  /*Mescla um array ao array do nó*/
  public function merge($array)
  { 
     if(is_array($array)){
		$this->arr = array_merge($this->arr, $array);
		return true;
	 }else return false;   
  }
  
  public function mergeFile($file,$local=true){
	  $file = new Json($file,$local);
	  $this->merge($file->gets());
  }
  
  /*Substitui o array atual do nó por outro*/
  public function replace($array)
  { 
      if(is_array($array)){
		 $this->arr = $array; 
		 return true;
	  }else return false;   
  }
  
  /*Elimina um elemento do nó de dados*/
  public function del($key)
  {
	 if(isset($this->arr[$key])){
		  unset($this->arr[$key]);
		  return true;
	 }else return false;
  }
  
  private function for_utf8($string){
	  $ar = ['Á','É','Í','Ó','Ú','á','é','í','ó','ú','Â','Ê','Ô','â','ê','ô','À','à','Ü','ü','Ç','ç','Ã','Õ','ã','õ','Ñ','ñ','&','“','‘','<','>','/','\\'];
	  $as = ['&Aacute;','&Eacute;','&Iacute;','&Oacute;','&Uacute;','&aacute;','&eacute;','&iacute;','&oacute;','&uacute;','&Acirc;','&Ecirc;','&Ocirc;','&acirc;','&ecirc;','&ocirc;','&Agrave;','&agrave;','&Uuml;','&uuml;','&Ccedil;','&ccedil;','&Atilde;','&Otilde;','&atilde;','&otilde;','&Ntilde;','&ntilde;','&amp;','&quot;','‘','&lt;','&gt;','\/','\\'];
	  return str_replace($as,$ar,$string);
  }
  
  /*Salva o arquivo com as alterações*/
  public function save($format=true)
  {  
	 $filename = $this->revert_utf8($this->filename,true); 
	 /*Se format for true, o json será salvo de forma formatada*/
	 if($format){
		$data = json_encode($this->arr,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
	 }else{
		 $data = json_encode($this->arr,JSON_UNESCAPED_UNICODE); 
	 }
	  //var_dump($this->for_utf8($data));
	 return file_put_contents($filename,$this->for_utf8($data),FILE_TEXT); 
  }
  
  public function create($filename, $format=false, $sobrepor=false)
  {
	 /*Se format for true, o json será salvo de forma formatada*/
	 if($format){
		$data = json_encode($this->arr,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
	 }else{
		 $data = json_encode($this->arr,JSON_UNESCAPED_UNICODE); 
	 }
	if(!file_exists($filename)){
		return file_put_contents($filename,$data);
	}elseif(file_exists($filename) && $sobrepor == true){
		return file_put_contents($filename,$data);
	}else return false;
  }
  
  /*Apaga um arquivo caso ele exista*/
  public function delete($filename)
  {
	if(file_exists($filename)){
		return unlink($filename);
	}else return false;
  }
  
  /*Instancia da classe de forma estática*/
  public static function url($url)
  {
	  $x = new Json($url);
	  return $x;
  }
  
  public function revert_utf8($string,$r=false)
  {
	$c = array('À','Á','Ã','Â','É','Ê','Í','Ó','Õ','Ô','Ú','Ü','Ç','Ñ','à','á','ã','â','é','ê','í','ó','õ','ô','ú','ü','ç','ñ');
	
    $s = array('xx1x','xx2x','xx3x','xx4x','xx5x','xx6x','xx7x','xx8x','xx9x','x1xx','x11x','x12x','x13x','x14x','x15x','x16x','x17x','x18x','x19x','x2xx','x21x','x22x','x23x','x24x','x25x','x26x','x27x','x28x');
	
	if($r){
		$string = str_replace($s,$c,$string);
	}else{
		$string = str_replace($c,$s,$string);
	}
  	return $string;
  }
}