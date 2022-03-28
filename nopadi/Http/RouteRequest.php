<?php

namespace Nopadi\Http;

use Nopadi\Http\URI;
use Nopadi\Http\RouteCallback;
use Exception;

class RouteRequest extends RouteCollection
{

	private $code = 404;

	public function response()
	{
		/*Verifica qual é o método da solicitação e carrega só as rotas correspondente ao metodo iniciado*/
		$Http = self::all($_SERVER['REQUEST_METHOD']);
		/*Verifica se a URL atual existe*/
		$base = new URI();
		$url = $base->base();
		$uri = $base->uri();
		

		if ($url != $uri) {
			/*varre toda a matriz de rotas*/
			foreach ($Http as $route => $param) {

				$uri = explode('?', $uri);
				$uri = $uri[0];

				if (preg_match("/^{$route}$/i", $uri)) {

					$callback = $param['callback'];
					$namespace = $param['namespace'];
					$params = $param['params'];
					$middleware = $param['middleware'];
					$execute = new RouteCallback();

					$execute->before($middleware);
					
					if(http_response_code() == 401)
					{ 
					   $execute->stop();  $this->code = 401; 
					}else{
						$this->code = 200;
					}

					$execute->execute($callback, $namespace, $params);
					
				}
			}
		}

		if ($url == $uri) {
			if (array_key_exists('np-route-index', $Http)) {

				$param = $Http['np-route-index'];
				$callback = $param['callback'];
				$namespace = $param['namespace'];
				$params = $param['params'];

				$execute = new RouteCallback();

				$execute->execute($callback, $namespace, $params);

				$this->code = 200;
			}
		}

		if ($this->code == 404) {
			header('HTTP/1.1 404 Not Found');
			if (array_key_exists('np-route-404', $Http)) {
               
				$param = $Http['np-route-404'];
				$callback = $param['callback'];
				$namespace = $param['namespace'];
				$params = $param['params'];

				$execute = new RouteCallback();

				$execute->execute($callback, $namespace, $params);

				$this->code = 400;
			}
		}


		if ($this->code == 404) {
            header('HTTP/1.1 404 Not Found');
			throw new Exception('404 - Routing does not exist. The route you are trying to access is no longer available or has not been defined by the developer. 404 - Roteamento inexistente. A rota que você está tentando acessar não está mais disponível ou não foi definida pelo desenvolvedor.');
		}
	}
}
