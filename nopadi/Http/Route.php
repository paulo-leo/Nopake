<?php
/*
*Classe para declaração de rotas
*Autor:Paulo Leonardo da Silva Cassimiro
*/
namespace  Nopadi\Http;

use  Nopadi\Http\URI;
use  Nopadi\MVC\View;
use  Nopadi\Http\Param;
use  Nopadi\Http\RouteCollection;

class Route extends RouteCollection
{
    private static $group;
    private static $args;
 
    private static function setArgs($args=null)
    {
        /*array de middleware que serão executados*/
        $middleware = isset($args['middleware']) ? $args['middleware'] : [];
    
        /*variaveis que serão inicializadas na montagem da rota:array*/
        $params = isset($args['params']) ? $args['params'] : array();
    
        /*pacote de onde será carregado a classe de controle*/
        $namespace = isset($args['namespace']) ? $args['namespace'] : 'App\Controllers\\';
    
        $prefix = isset($args['prefix']) ? $args['prefix'].'/' : null;

    
     return array(
    'namespace'=>$namespace,
    'middleware'=>$middleware,
    'params'=>$params,
    'prefix'=>$prefix);
    }
    /*Cria uma rota*/
    public static function create($type, $route, $callback, $args=null)
    {
		$callback = is_string($callback) ? str_ireplace('/','\\',$callback) : $callback;
        
        $route = str_ireplace('.', '/', $route);
        $route = (strlen($route) > 1 && substr($route, 0,1) == '/') ? substr($route,1) : $route;
        
        $args = (!is_null(self::$group)) ? self::setArgs(self::$group) : self::setArgs($args);

        $base = new URI();
        $base = $base->base();
    
        $middleware = $args['middleware'];
        $params = $args['params'];
		
		if(is_string($callback) && substr($callback,0,1) == '@'){
			$callback = substr($callback,1);
			$namespace = 'Modules\\';
		}else{
			$namespace = $args['namespace'];
		}
		

        $prefix = $args['prefix'];
    
        if ($route == '@prefix' || $route == '@') {
            $route = substr($prefix, 0, -1);
        } else {
            $route = $prefix.$route;
        }
    
        $route = str_ireplace('.', '\/', $route);
        if ($route == '/') {
            $route = 'np-route-index';
        } elseif ($route == '*' || $route == '404') {
            $route = 'np-route-404';
        } else {
            $route = $base.$route;
        }

        /*garante que type está dentro do contexto*/
        $type= strtoupper($type);
        $type = (($type == 'POST') || ($type == 'PUT') || ($type == 'DELETE') || ($type == 'PATCH')) ? $type : 'GET';
        $args = array(
         'callback'=>$callback,
         'middleware'=>$middleware,
         'namespace'=>$namespace,
         'params'=>$params);
   
        self::add($type, $route, $args);
    }
	
    /*Pemite o acesso total a aplicação*/
    public static function access($access='*')
    {
      header("Access-Control-Allow-Origin: {$access}");
	  header("Status: 200 ok");
      header("Access-Control-Allow-Credentials: true");
      header("Access-Control-Max-Age: 1000");
      header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding, App-Key");
      header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
    }
    
	
    /*Cria um grupo de rotas com argumentos*/
    public static function group($args, $callback=null)
    {
        self::$group = $args;
    
        if (is_callable($callback)) {
            call_user_func($callback);
        }
    
        self::$group = null;
    }
	
    /*Cria uma rota com o verbo GET*/
    public static function get($route, $callback, $args=null)
    {
        self::create('GET', $route, $callback, $args);
    }
	
	/*Cria uma rota com o verbo PATCH*/
    public static function patch($route, $callback, $args=null)
    {
        self::create('PATCH', $route, $callback, $args);
    }
	/*CONNECT*/
	public static function connect($route, $callback, $args=null)
    {
        self::create('CONNECT', $route, $callback, $args);
    }
	
	/*TRACE*/
	public static function trace($route, $callback, $args=null)
    {
        self::create('TRACE', $route, $callback, $args);
    }
	
	/*HEAD*/
	public static function head($route, $callback, $args=null)
    {
        self::create('HEAD', $route, $callback, $args);
    }
	
	/*OPTIONS*/
	public static function options($route, $callback, $args=null)
    {
        self::create('OPTIONS', $route, $callback, $args);
    }
	
    /*Cria uma rota com o verbo POST*/
    public static function post($route, $callback, $args=null)
    {
        self::create('POST', $route, $callback, $args);
    }
	
    /*Cria uma rota com o verbo PUT*/
    public static function put($route, $callback, $args=null)
    {
        self::create('PUT', $route, $callback, $args);
    }
	
    /*Cria uma rota com o verbo DELETE*/
    public static function delete($route, $callback, $args=null)
    {
        self::create('DELETE', $route, $callback, $args);
    }
	
    /*Cria uma rota com todos os verbos*/
    public static function any($route, $callback, $args=null)
    {
        self::get($route, $callback, $args);
        self::post($route, $callback, $args);
        self::put($route, $callback, $args);
        self::delete($route, $callback, $args);
    }
   
    /*Cria um conjunto de recursos*/
    public static function resources($route, $callback, $args=null)
    {
	   /*Rota especifica para exibir todos os recursos*/
       self::get($route, $callback.'@index', $args);
		
	   $id = Param::lastInt();
	   
	   if($id){ self::get($route.'/{id}', $callback.'@show', $args); 
	   }else{
		 /*Rota especifica para exibir todos os registros no formato JSON*/
         self::get($route.'/records', $callback.'@records', $args);
		 /*Rota especifica para exibir um registro no formato JSON*/
		 self::get($route.'/record', $callback.'@record', $args);
		 self::get($route.'/record/{$id}', $callback.'@record', $args);
  
        /*Rota especifica para exibir uma página inicial*/
         self::get($route.'/home', $callback.'@home', $args);
	    /*Rota especifica para exibir um formulário para criar um recurso*/
         self::get($route.'/create', $callback.'@create', $args);
		 /*Rota para exibir o total de registros*/
         self::get($route.'/total', $callback.'@total', $args);
		 /*Rota para exibir um filtro*/
         self::get($route.'/filter', $callback.'@filter', $args);
	    /*Rota especifica para servir uma página de ajuda para um recuso*/
        self::get($route.'/help', $callback.'@help', $args);
	    /*Rota especifico para burscas de recursos*/
        self::post($route.'/search', $callback.'@search', $args);
		
	   }
		
		/*Rota especifica para editar um recurso*/
        self::get($route.'/{id}/edit', $callback.'@edit', $args);
	    /*Rota especifica para envio de arquivos*/
        self::post($route.'/upload', $callback.'@upload', $args);
		/*Rota especifico para criar um recurso*/
        self::post($route, $callback.'@store', $args);
		/*Rota para atualiza um recurso*/
         self::put($route, $callback.'@update', $args);
		/*Rota para atualiza um recurso pelo id*/
        self::put($route.'/{id}', $callback.'@up', $args);
		/*Rota para apaga um recurso*/
        self::delete($route, $callback.'@destroy', $args);
		/*Rota para apaga um recurso pelo id*/
		self::delete($route.'/{id}', $callback.'@del', $args);
    }
	
    /*Redireciona o usuário*/
    public static function redirect($to=null)
    {
        $base = new URI();
        $base = $base->base();
        $to = ($to == '/') ? null : $to;
        $base = $base.$to;
        header('Location:'.$base);
    }
	
    /*Cria diversos controles para mesma classe*/
    public static function controllers($array, $class, $args=null)
    {
        foreach ($array as $key=>$controller) {
            $key = explode(':', $key);
         
            $method  = (isset($key[1])) ? strtoupper($key[0]) : 'GET';
            $route  =  (isset($key[1])) ? $key[1] : $key[0];
            $controller = $class.'@'.$controller;
         
        switch($method)
		 {
             case 'POST': self::post($route, $controller, $args); break;
             case 'PUT': self::put($route, $controller, $args); break;
             case 'DELETE': self::delete($route, $controller, $args); break;
             case 'ANY': self::any($route, $controller, $args); break;
             default: self::get($route, $controller, $args);
         }
       }
    }
	/*Pega os parametros da rota via path*/
	public static function path($x){
		return Param::get($x);
	}
	
	/*Carrega um módulo especifico*/
    public static function module($module)
    {
        $controller = 'Modules\\'.$module.'\\'.$module;
		$method = 'main';
		
		$rc = new \ReflectionClass($controller);

        

	     if($rc->isInstantiable() && $rc->hasMethod($method))
		{
			        $params = array();
					return call_user_func_array(array(new $controller, $method), array_values($params));
					
	    } else{

			    throw new \Exception("Nopadi: Erro ao execultar callback: controller não pode ser instanciado, ou método não exite");				
		}
		
    }
}
