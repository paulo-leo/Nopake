<?php
/*
*Classe para paramenização da rota
*Autor:Paulo Leonardo da Silva Cassimiro
*/
namespace  Nopadi\Http;

use Nopadi\Http\URI;

class Param
{
    private static $param;
	
	/*Registra um parâmetro de rota*/
	public static function set($param){
		
		$er1 = '{([A-Za-zÀ-ú0-9\.\-\_]+)}';
		$size = explode('/',$param);
		
		$param = preg_replace("/{$er1}/simU", '{string}', $param);
		
		self::$param[$param] = array('size'=>count($size),'index'=>$size);
	}
	
	/*Obtem o valor do parametro de acordo com a chave*/
	public static function get($ind=null){
		$base = new URI();
		$uri = explode('?',$base->uri());
        $uri = $uri[0];
		 
		$value = null;
		foreach (self::$param as $key => $val){
			
			$er1 = '([A-Za-zÀ-ú0-9\.\-\_]+)';
		    $key = str_ireplace(['/','{string}'],['\/',$er1],$key);
			
			if(preg_match("/^{$key}$/i", $uri)){
				$search = array_search('{'.$ind.'}',$val['index']);
				if($search) $value = self::value($search);
			}
		}	
		return htmlspecialchars(trim($value), ENT_QUOTES);
	}
	
	/*Obtem o valor inteiro do parametro de acordo com a chave*/
	public static function int($key){
		$key = self::get($key);
		if(is_numeric($key)) return intval($key);
	}
	
	public static function getInt($key)
	{
		return self::int($key);
	}
	
	public static function getFloat($key)
	{
		return self::float($key);
	}
	
	/*Obtem o valor flutuante do parametro de acordo com a chave*/
	public static function float($key){
		$key = self::get($key);
		if(is_numeric($key)) return floatval($key);
	}
	
	/*Verfica se o parametro corresponde ao valor informado*/
	public static function is($key,$value)
	{
		$key = self::get($key);
		if($key == $value) 
			return true;
		else return false;
	}
	
	/*Obtem o valor da rota*/
	private static function value($index)
    {
		$resource = new URI();
		$resource = explode('?',$resource->uri());
		$resource = $resource[0];
		
        $resource = explode('/', $resource);
        $value = $resource[$index];
        return trim(htmlspecialchars($value, ENT_QUOTES));
    }
	
	public static function first()
    {
		$resource = new URI();
		$base = $resource->base();
		$uri = explode('?',$resource->uri());
		$uri = $uri[0];
		$uri = str_ireplace($base,'',$uri);
		$uri = explode('/', $uri);
		return trim(htmlspecialchars($uri[0], ENT_QUOTES));
    }
	/*Obtem o parametro de acordo com o índice númerico informado*/
	public static function getIndex($index=0,$value=null)
    {
		$resource = new URI();
		$base = $resource->base();
		$uri = explode('?',$resource->uri());
		$uri = $uri[0];
		$uri = str_ireplace($base,'',$uri);
		$uri = explode('/', $uri);
		
		if(is_null($value)){
		  $index = isset($uri[$index]) ? trim(htmlspecialchars($uri[$index], ENT_QUOTES)) : false;	
		}else{
			$index = isset($uri[$index]) && (strtolower(trim($uri[$index])) == strtolower(trim($value))) ? trim(htmlspecialchars($uri[$index], ENT_QUOTES)) : false;
		}
		
		
		return $index;
    }
	
	public static function last()
    {
		$resource = new URI();
		$resource = explode('?',$resource->uri());
		$resource = $resource[0];
        $resource = explode('/', $resource);
		$size = count($resource);
        $value = $resource[$size - 1];
        return trim(htmlspecialchars($value, ENT_QUOTES));
    }
	
	public static function lastInt(){
		$id = self::last();
		return is_numeric($id) ? $id : false;
	}
	/*Retona a URL/Rota do recurso atual*/
	public static function route($queryIgnore=true)
    {
		$uri = new URI();
		$base = $uri->base();
		
		if($queryIgnore)
		{
		  $url = explode('?',$uri->uri());
		  $url = $url[0];	
		}else
		{
		  $url = $uri->uri();
		}
		
        $route = substr($url,strlen($base));
        return trim(htmlspecialchars($route, ENT_QUOTES));
    }
	
	public static function path($queryIgnore=true)
	{
		return self::route($queryIgnore);
	}
	
	/*Verifca se a rota atual corresponde a rota informada no parametro*/
	public static function isRoute($route)
    {
		 $is =  self::route();
		
		 $route = str_ireplace('/','\/',$route);
		 $route = str_ireplace('{id}','([0-9]+)',$route);
		 $route = str_ireplace('{string}','([A-Za-zÀ-ú0-9\.\-\_]+)',$route);
		 $route = str_ireplace('{letter}','([A-Za-zÀ-ú]+)',$route);
		 
		 return preg_match("/^{$route}$/i", $is) ? $is : false;
    }
}
