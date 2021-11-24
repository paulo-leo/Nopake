<?php
/*
*
*Author: Paulo Leonardo da Silva Cassimiro
*
*/

namespace Nopadi\Support;

class ServiceProvider
{
	private static $block = array();

	/*Realiza o bloqueio da execução, podendo escrever uma mensagem personalizada no status*/
	final protected static function status($key, $msg)
	{

		self::$block[$key] = $msg;
	}
	/*Retorna a mensagem dos bloqueios dos serviços*/
	final public static function message()
	{
		$msg = null;
		foreach (self::$block as $id) {
			$msg .= $id;
		}
		hello(alert($msg));
	}
	/*Verifica se a execução está bloqueada*/
	final public static function execute()
	{
		if (count(self::$block) > 0) {
			return false;
		} else return true;
	}

	/*Configura e executa um serviço*/
	protected function boot()
	{
	}

	final public function bootServices($kernel)
	{
		foreach ($kernel as $providerService) {

			$method = 'boot';

			$rc = new \ReflectionClass($providerService);

			if ($rc->isInstantiable() && $rc->hasMethod($method)) {
				call_user_func_array(array(new $providerService, $method), []);
			}
		}
	}
}
