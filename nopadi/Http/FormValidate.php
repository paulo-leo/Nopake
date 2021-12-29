<?php
/*
Classe para validação de dados
*/
namespace Nopadi\Http;

class FormValidate
{
  private $data;
  private $errors;
  
  
  public function __construct($data)
  {
	$this->data = $data;  
  }
  
  /*Obtem o type*/
  private function getType($val)
  {
	 $val = explode('|',$val);
	 
	 if(in_array('number',$val)) return 'number';
	 elseif(in_array('date',$val)) return 'date';
	 elseif(in_array('datetime',$val)) return 'datetime';
	 else return 'string';
	
  }
  
  private function checkType($key,$type)
  {
	 $value = $this->data[$key];
	 $msg = null;
	 
     if($type == 'number')
	 {
		if(!is_numeric($value)) $msg = "O campo {$key} deve ser do tipo númerico.";  
	 } 
     elseif($type == 'datetime')
	 {
		if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-9]){2}:([0-9]){2}:([0-9]){2}$/",$value)) $msg = "O campo {$key} deve ser do tipo data e hora.";  
	 }	
	 elseif($type == 'date')
	 {
		if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$value)) $msg = "O campo {$key} deve ser do tipo data.";  
	 }	

     return $msg;	 
  }
  
  public function validate($arr)
  {
	foreach($arr as $key=>$val)
	{
		if(array_key_exists($key,$this->data))
		{
			$value = $this->data[$key];
			$type = $this->getType($val);
			$type = $this->checkType($key,$type);
			
			if($type)
			{
			  $this->errors[$key] = $type;
			}
			
		}else
		{
			$this->errors[$key] = "O campo {$key} é obrigatório!";
		}
	}  
  }
  public function getErrors()
  {
	  return $this->errors;
  }
}
