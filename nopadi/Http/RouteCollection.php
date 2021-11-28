<?php
namespace Nopadi\Http;

use Nopadi\Http\Param;

class RouteCollection 
{
	//Armazena as rotas
	protected static $route_get = [];
	protected static $route_post = [];
	protected static $route_put = [];
	protected static $route_delete = [];
	protected static $route_patch = [];
	protected static $route_connect = [];
	protected static $route_trace = [];
	protected static $route_head = [];
	protected static $route_options = [];
	
	/*Cria e armazena a rota*/
	protected static function add($type,$route,$args=null){
		
         Param::set($route);
		 
		 $er = '{([A-Za-zÀ-ú0-9\.\-\_]+)}';
		 $route = preg_replace("/{$er}/simU", '{string}', $route);
		 $route = str_ireplace('/','\/',$route);
		 $route = str_ireplace('{string}','([A-Za-zÀ-ú0-9\.\-\_]+)',$route);
		 
		 
		$args = array(
		'callback'=>$args['callback'],
		'namespace'=>$args['namespace'],
		'params'=>$args['params'],
		'middleware'=>$args['middleware']
		);
		
		switch($type){
			case 'POST' : self::$route_post[$route] = $args; break;
			case 'PUT' : self::$route_put[$route] =$args; break;
			case 'DELETE' : self::$route_delete[$route] = $args; break;
			case 'PATCH' : self::$route_patch[$route] = $args; break;
			case 'CONNECT' : self::$route_connect[$route] = $args; break;
			case 'TRACE' : self::$route_trace[$route] = $args; break;
			case 'HEAD' : self::$route_head[$route] = $args; break;
			case 'OPTIONS' : self::$route_options[$route] = $args; break;
			default : self::$route_get[$route] = $args; break;
		}
	}
	//Obtem todas as rotas armazenadas
	public static function all($type){
		switch($type){
			case 'POST' : return self::$route_post; break;
			case 'PUT' :  return self::$route_put; break;
			case 'DELETE' :  return self::$route_delete; break;
			case 'PATCH' :  return self::$route_patch; break;
			case 'CONNECT' : return self::$route_connect; break;
			case 'TRACE' : return self::$route_trace; break;
			case 'HEAD' : return self::$route_head; break;
			case 'OPTIONS' : return self::$route_options; break;
			default :  return self::$route_get; break;
		}
	}
}