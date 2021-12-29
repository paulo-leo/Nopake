<?php

namespace Nopadi\Http;

use Nopadi\Base\DB;
use Nopadi\Http\URI;

class Request
{
	private $all;
	private $check;
	private $files;
	private $erros;
	private $values;
	private $allHeaders;

	public function __construct()
	{
	  $this->erros = array();
	  $this->values = array();
	  $this->files = array();
	  $this->check = 1;
      switch ($_SERVER['REQUEST_METHOD']) 
	  {
           case 'GET':
				$this->all = isset($_GET) ? $_GET : array();
				break;
			case 'POST':
				$this->all = isset($_POST) ? $_POST : array();
				$this->files = isset($_FILES) ? $_FILES : array();
				break;
			default:
				$_REQUEST_NOPADI = file_get_contents('php://input');
				parse_str($_REQUEST_NOPADI, $_REQUEST_NOPADI);
				$this->all = isset($_REQUEST_NOPADI) ? $_REQUEST_NOPADI : array();
				break;
		}
		
		/*Salva todos os headers dentro de um array associativo*/
		$this->allHeaders = $this->setAllHeaders();	
	}
	
	private function setAllHeaders()
	{
		$arr = array();
		foreach(getallheaders() as $key=>$val)
		{
			$arr[strtolower($key)] = $val;
		}
		return $arr;
	}
	
	public function __destruct() {
         if($this->check == 1){
			 if (!isset($_SESSION)) session_start();
	         if(isset($_SESSION['np_errors'])){
				 unset($_SESSION['np_errors']);
			 }
		 }
    }
	
	/*Salva os erros em um array de sessão*/
	private function setError($name,$message)
	{
	  $this->check *= 0; 

	  if (!isset($_SESSION)) session_start();
	  
	  $_SESSION['np_errors'][$name] = $message;
	  $this->erros[$name] = $message;
	}
	
	/*Retonar true caso não exista nenhum erro*/
	public function checkError()
	{
	  if($this->check == 1) return true; else return false;	  
	}
	
	/*Salva os erros em um array de sessão*/
	public function getError($name=null)
	{
	  return is_null($name) ? $this->erros : $this->erros[$name];
	}
	
	/*Retorna uma string com todas as mensagens de erro*/
	public function getErrorMessage($br='<br>')
	{
	  $message = null;
	  if(count($this->erros) >= 1){
	  foreach($this->erros as $name=>$value)
	  {
		 $message .= '['.strtolower($name).'] '.$value.$br;
	  }}
	  return $message;
	}
	
	/*Retorna um array com todas as mensagens de erro*/
	public function errors()
	{
	  $message = array();
	  if(count($this->erros) >= 1){
	  foreach($this->erros as $name=>$value)
	  {
		 $name = ucfirst($name);
		 $message[] = $name.' '.$value;
	  }}
	  return $message;
	}
	
	public function route($position = 1)
	{
		$base = new URI();
		$uri = $base->uri();
		$uri = explode('/', $uri);
		$count = count($uri);
		if (isset($uri[$count - $position])) {
			$uri = $uri[$count - $position];
			$uri = explode('?', $uri);
			return $uri[0];
		} else return false;
	}

    private function noSpecial($all)
	{	
		$keys = array('_event','_token','_automation','_action','_method');
		
		foreach($keys as $key)
		{
			if(array_key_exists($key, $all)) unset($all[$key]);
		}
		
		return $all;
	}

	/*Retorna uma array com todas as chaves*/
	public function all($except = null)
	{
		if (!is_null($except)) 
		{
			$this->except($except);
		}
		$all = $this->all;

		return $this->noSpecial($all);
	}

     /*Executa um método de automação*/
    public function _automation()
	{		
		return $this->get('_automation');
	}

    /*retorna a ação enviado no momento do recurso*/
	public function _action()
	{
		return $this->get('_action');
	}

	/*retorna o evento enviado no momento do recurso*/
	public function _event()
	{
		return $this->get('_event');
	}
	
	/*retorna o token no momento do recurso*/
	public function _token()
	{
		return $this->get('_token');
	}
	/*retorna o method personalizado no momento do recurso*/
	public function _method()
	{
		return $this->get('_method');
	}
	
	/*retorna um header especifico*/
	public function getHeader($key)
	{
	   $key = strtolower($key);
	   $header = $this->allHeaders;
       return array_key_exists($key,$header) ? $header[$key] : null;
	}
	
	/*retorna todos os headers*/
	public function headers()
	{
	   return $this->allHeaders;
	}
	
	/*Substitui uma chave*/
	public function replace($key, $val)
	{
		if (array_key_exists($key, $this->all)) {
			$this->all[$key] = $val;
			return $this->all[$key];
		} else return false;
	}
	
	/*Retorna um array e exclui os valores das chaves informadas*/
	public function except($x)
	{
		if (is_string($x)) $x = array($x);
		for ($i = 0; $i < count($x); $i++) {
			if (array_key_exists($x[$i], $this->all)) {
				unset($this->all[$x[$i]]);
			}
		}
		return $this->all;
	}
	/*Retorna um array somente com as chaves informadas*/
	public function only($x)
	{
		$ar = array();
		for ($i = 0; $i < count($x); $i++) {
			if (array_key_exists($x[$i], $this->all)) {
				$ar[$x[$i]] = $this->all[$x[$i]];
			}
		}
		return $ar;
	}
	//!empty($this->all[$x])
	
	/*Retorna o array de arquivos*/
	public function getFile($file_name,$file_value=null)
	{
		$value = null;
		$files = $this->files;
		if(array_key_exists($file_name,$files))
		{
		  if(!is_null($file_value))
		  {
			if(isset($files[$file_name][$file_value]))
			{
				$value = $files[$file_name][$file_value];
			}
		  }else{
			  $value = $files[$file_name];
		  }
		}
		return $value;
	}
	
	/*Retorna um valor de uma chave informada*/
	public function get($x, $dafault = null)
	{
		if ((array_key_exists($x, $this->all)) && (!empty($this->all[$x]) || is_numeric($this->all[$x]))) {
			$x = $this->all[$x];
			$x = htmlspecialchars($x, ENT_QUOTES, "UTF-8");
		} elseif (!is_null($dafault)) {
			$x = $dafault;
			$x = htmlspecialchars($x, ENT_QUOTES, "UTF-8");
		}else{
			$x = null;
		}
		return $x;
	}
	
	private function setValue($name,$value=null)
	{
		$this->values[$name] = $value;
	}
	
	
	public function getValues()
	{
		return $this->values;
	}
	
	public function getString($x,$dafault=null,$msg_error=null)
	{
		$var = $this->get($x);
		if(!is_null($dafault))
		{
			$dafault = explode(':',$dafault);
		    $min = intval($dafault[0]);
		    $max = isset($dafault[1]) ? intval($dafault[1]) : $min;
			$msg = $min == $max ? "deve ter no mínimo {$min} caracteres." : "deve ter entre {$min} e {$max} caracteres.";
			
			if(strlen($var) >= $min && strlen($var) <= $max){
				$var = (string) $var;
				$var = substr($var,0,$max);
				$this->setValue($x,$var);
				return $var;
			}else{
				$msg_error = is_null($msg_error) ? $msg : $msg_error;
				$this->setError($x,$msg_error);
			}
		}else{
			$this->setValue($x,$var);
			return $var;  
		}
	}
	
	public function getUnique($x,$table,$id = null,$msg=null)
	{
		$msg = is_null($msg) ? 'já está registrado na base de dados com o valor informado!' : $msg;
		$var = trim($this->get($x));
		if(strlen($var) >= 1)
		{
			if(is_array($table)){
				$values = $table;
				$table = $table['table'];
				unset($values['table']);
			}else{
			    $values = array($x=>$var);
			}
			
			$table = DB::table($table);
			$table = $table->exists($values,$id);
			
			if($table){
			  $this->setError($x,$msg);
			}else{
			  $this->setValue($x,$var);	
			  return $var;
			}
		}else{
			$this->setError($x,"não pode ser vazio!");
		}
	}
    /*Valida um valor inteiro*/
	public function getInt($x,$dafault=null,$msg_error=null)
	{
		$var = $this->get($x,$dafault);
		$var = str_ireplace(['/','.','-',',','_','(',')','*',' '],'',$var);
		
		if(is_numeric($var)){
			$var = (int) $var;
			$this->setValue($x,$var);
			return $var; 
		}else{
		  $msg_error = is_null($msg_error) ? 'deve possuir um valor numérico do tipo inteiro!': $msg_error;
		  $this->setError($x,$msg_error);
		} 
	}
	/*Pega qualquer valor, muito usado na não inexistencia de determinada solicitação*/
	public function getAny($x,$dafault=null)
	{
		$var = $this->get($x,$dafault);
		if(!is_null($var)){
			$this->setValue($x,$var);
			return $var; 
		}
	}
	
	public function getFloat($x,$dafault=null,$msg=null)
	{
		$var = $this->get($x,$dafault);
		if(is_numeric($var))
		{
			$var = (float) $var;
			$this->setValue($x,$var);
			return $var; 
		}else{
			$msg = is_null($msg) ? 'deve possuir um valor numérico do tipo float!' : $msg;
			$this->setError($x,$msg);	
	   }
	}
	
	public function getDate($x,$dafault=null,$msg=null)
	{
        $var = strtolower($this->get($x,$dafault));
	    $sub = "/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/i";
        $var = preg_replace($sub, "$3-$2-$1", $var);
		$er = "([0-9]{4})-([0-9]{2})-([0-9]{2})";
		if(preg_match("/^{$er}/i",$var)){
			$var = substr($var,0,10);
			$this->setValue($x,$var);
			return $var;
		}else{
			$msg = is_null($msg) ? 'deve possuir um valor no formato de data: AAAA-MM-DD.' : $msg;
			$this->setError($x,$msg);	
		}  
	}
	
	public function getTime($x,$dafault=null,$msg=null)
	{
        $var = strtolower($this->get($x,$dafault));
		$var = str_ireplace("-",":",$var);
		$var = strlen($var) == 5 ? $var.":00" : $var;
		
		$er = "([0-9]{2}):([0-9]{2}):([0-9]{2})";
		if(preg_match("/^{$er}/i",$var)){
			 $this->setValue($x,$var);
			 return $var; 
		}else{
			$msg = is_null($msg) ? 'deve possuir um valor no formato de hora: HH:MM' : $msg;
			$this->setError($x,$msg);	
		}
	}
	
	public function getDatetime($x,$dafault=null,$msg=null)
	{
        $var = strtolower($this->get($x,$dafault));
		$var = strlen($var) == 10 ? $var." 00:00:00" : $var;
		
	    $sub = "/([0-9]{2})\/([0-9]{2})\/([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/i";
        $var = preg_replace($sub, "$3-$2-$1 $4:$5:$6", $var);
		$er = "([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})";
		if(preg_match("/^{$er}/i",$var)){
			$this->setValue($x,$var);
			return $var; 
		}else{
			$msg = is_null($msg) ? 'deve possuir um valor no formato de data e hora AAAA-MM-DD HH:MM:SS.' : $msg;
			$this->setError($x,$msg);	
		} 
	}
	
    /*Valida e retorna valor do tipo monetário*/
	public function getMoney($x,$dafault=null,$msg=null)
	{
      $money = $this->get($x,$dafault);
      $money = str_ireplace(['R','$','€',',',' '],['','','','.',''],$money);
   
      $p1 = substr($money,-3,1);
      $c1 = substr($money,-2,2);
      $p2 = substr($money,-2,1);
      $c2 = substr($money,-1,1);
   
     $money = str_ireplace('.','',$money);

   if($p1 == '.'){
	   $c = $c1; 
	   $money = substr($money,0,-2);
   }elseif($p2 == '.'){
	   $c = $c2.'0'; 
	   $money = substr($money,0,-1);
   }else{ $c = '00'; };
   
      $money .= '.'.$c;
      $money =  is_numeric($money) ? (float) $money : null;
	  if($money){
		  $this->setValue($x,$money);
		  return $money;
	  }else{
		 $msg = is_null($msg) ? 'deve possuir um valor do tipo monetário.' : $msg; 
		 $this->setError($x,$msg); 
	  }
	}
	
	public function getEmail($x,$dafault=null,$msg=null)
	{
        $var = strtolower($this->get($x,$dafault));
		if(filter_var($var, FILTER_VALIDATE_EMAIL)){
			$this->setValue($x,$var);
			return $var;
		}else{
			 $msg = is_null($msg) ? 'deve possuir um valor no formato de e-mail.' : $msg; 
			$this->setError($x,$msg); 
			return false;
		}
	}
	
	public function getBool($x,$dafault=false)
	{
        $var = strtolower($this->get($x,$dafault));
		$bool = ($var || $var == 'on' || $var == 1 || $var == '1') ? true : false;
	    $this->setValue($x,$bool);
		return $bool;
	}
	
	public function getBit($x,$dafault=null)
	{
		$dafault = ($dafault == true || strtolower($dafault) == 'on') ? 'on' : 'off';
	    $on = $this->get($x,$dafault);
		$on = is_numeric($on) && $on > 0 ? 1 : $on;
		$var = ($on == 'on' || $on == 1) ? 1 : 0;
		$this->setValue($x,$var);
		return $var;
	}
	
	public function getOn($x,$dafault=null)
	{
		$dafault = ($dafault == true || strtolower($dafault) == 'on') ? 'on' : 'off';
	    $on = $this->get($x,$dafault);
		$on = is_numeric($on) && $on > 0 ? 1 : $on;
		$var = ($on == 'on' || $on == 1) ? 'on' : 'off';
		$this->setValue($x,$var);
		return $var;
	}

	/*define um valor de uma chave informada*/
	public function set($x, $y = null)
	{
		if (!array_key_exists($x, $this->all)) {
			$this->all[$x] = $y;
		}
	}

	/*Verifica se uma determinada chave existe e se não está vazia*/
	public function has($x)
	{
		if (array_key_exists($x, $this->all)) return true;
		else return false;
	}

	/*Verifica se uma determinada chave existe no array Request*/
	public function exists($x)
	{
		$v = 1;
		for ($i = 0; $i < count($x); $i++) {
			if (array_key_exists($x[$i], $this->all)) {
				$v *= 1;
			} else {
				$v *= 0;
			}
		}
		if ($v) return true;
		else return false;
	}
	/*Retorna um array com todas as chaves do array do Objeto Request*/
	public function keys()
	{
		$ar = array();
		foreach ($this->all as $key => $val) {
			$ar[] = $key;
		}
		return $ar;
	}

	/*Checa se todas as regras definidas no array são verdadeiras*/
	public function check($x)
	{
		$v = 1;
		foreach ($x as $key => $val) {
			if (array_key_exists($key, $this->all) && !empty($this->all[$key])) {

				$max = isset($val['max']) ? $val['max'] : null;
				$min = isset($val['min']) ? $val['min'] : null;
				$type = isset($val['type']) ? $val['type'] : null;
				$reg = isset($val['reg']) ? $val['reg'] : null;

				$v *= $this->auxType($this->all[$key], $type);
				$v *= $this->auxMinMax($this->all[$key], $min, $max);

				if (!is_null($reg)) {
					if (!preg_match("/{$reg}/", $this->all[$key])) $v *= 0;
				}
			} else {
				$default = isset($val['default']) ? $val['default'] : null;
				if (!is_null($default)) {
					$this->all[$key] = $default;
					$v *= 1;
				} else {
					$v *= 0;
				}
			}
		}
		if ($v) return true;
		else return false;
	}

	public function count()
	{
		return count($this->all);
	}

	//Métodos auxiliadores
	private function auxType($x, $type)
	{
		if (!is_null($type)) {
			$type = strtolower($type);
			if ($type == "text") $type = "string";
			switch ($type) {
				case 'string':
					if (is_string($x)) return 1;
					else return 0;
					break;
				case 'float':
					if (is_float($x)) return 1;
					else return 0;
					break;
				case 'int':
					if (is_int($x)) return 1;
					else return 0;
					break;
				case 'number':
					if (is_numeric($x)) return 1;
					else return 0;
					break;
				case 'email':
					if (filter_var($x, FILTER_VALIDATE_EMAIL)) return 1;
					else return 0;
					break;
				case 'date':
					$x = explode('-', $x);
					if (count($x) > 1 && count($x) < 4) {
						if (checkdate($x[1], $x[2], $x[0])) return 1;
						else return 0;
					} else return 0;
					break;
			}
		} else return 1;
	}

	private function auxMinMax($x, $min, $max)
	{
		$v = 1;

		if (is_numeric($x)) $x = floatval($x);
		else $x = strlen($x);

		if ($min != null) {
			if ($x >= $min) $v *= 1;
			else $v *= 0;
		}
		if ($max != null) {
			if ($x <= $max) $v *= 1;
			else $v *= 0;
		}
		return $v;
	}

	/*Retorna instancia da classe Request*/
	public static function gets()
	{
		return new Request();
	}
	
	public static function isMethod($method='get')
	{
		$method_server = strtoupper($_SERVER['REQUEST_METHOD']);
		$method = trim(strtoupper($method));
		return ($method == $method_server) ? true : false;
	}
	
	public static function getMethod()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}
	public static function getAll()
	{
		return self::gets()->all();
	}
}
