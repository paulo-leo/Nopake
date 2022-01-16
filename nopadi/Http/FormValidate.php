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
  
   public function validate($validates)
   {
	  foreach($validates as $name=>$vals)
	  {
		  $vals = explode('|',$vals); 
		  $type = $this->getType($vals);
          $value = isset($this->data[$name]) ? $this->data[$name] : null;

		if($this->getValue('required',$vals,true))
		{
		  if($this->getValue('min',$vals))
		  {
			if(!$this->getMin($value,$vals)) $this->errors[] = 'Não atende o mínimo.';
		  }

		  if($this->getValue('max',$vals))
		  {
			if(!$this->getMax($value,$vals)) $this->errors[] = 'O máximo de caracteres.';
		  }
		} 
	  }
   }

   public function getErrors()
   {
	   return $this->errors;
   }

    /*Pega o minimo*/
    private function getMin($value,$values)
	{
		$min = $this->getValue('min',$values);
		$value = strlen($value);

        return ($value >= $min) ? true : false;
	}

	/*Pega o maximo*/
    private function getMax($value,$values)
	{
		$max = $this->getValue('max',$values);
		$value = strlen($value);

        return ($value <= $max) ? true : false;
	}

    /*Pega o tipo*/
    private function getType($values)
	{
		$types = array(
			'string',
			'number',
			'email',
			'date',
			'datetime',
			'time');

		$r = null;
		foreach($types as $type)
		{
			if($this->getValue($type,$values,$type))
			{
				$r = $type;
				break;
			} 
		}
		return $r;
	}

	private function checkType($key,$type)
	{
		$r = null;
		$value = isset($this->data[$key]) ? $this->data[$key] : null;
        if($value)
		{
			switch($type){
				case 'email' : 
					$r = filter_var($value, FILTER_VALIDATE_EMAIL);
				break;
				case 'number' : 
					$r = !is_numeric($value) ? true : null;
				break;
			}
			
		}
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
}
