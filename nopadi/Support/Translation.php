<?php
/*
*
*Author: Paulo Leonardo da Silva Cassimiro
*
*/

namespace Nopadi\Support;

use Nopadi\FS\Json;
use Nopadi\Http\Auth;

class Translation
{
	private $fileDir = 'storage/langs/';
	private $instance;
	private $register;

	/*Método para chamar o arquivo de tradução*/
	public function text($key)
	{
		$text = $this->instance->get($key);
		$text = $text ? $text : '[' . $key . ']';
		return $text;
	}

	public function val($key, $index = null, $default = null)
	{
		return $this->instance->val($key, $index, $default);
	}

	/*Retorna todas as chaves da tradução*/
	public function all()
	{
		return $this->instance->gets();
	}

	public function __construct()
	{
        $lang = $this->selectAutoLang();
		$file = $this->fileDir . $lang . '.json';
		$this->instance = new Json($file);
	}

	/*Mescla um arquivo de tradução que esteja no mesmo diretório*/
	public function merge($file)
	{
		$file = $this->fileDir . $file . '.json';
		$this->instance->mergeFile($file);
	}

	/*Mescla ou importa um arquivo de tradução que esteja em um diretório diferente dos arquivos de tradução atual*/
	public function import($file, $local = true)
	{
		if ($file != 'cache' || $file != 'path' || $file != 'import' || $file != 'module')
			$this->instance->mergeFile($file, $local);
	}
	
	/*Traduz a partir de um arquivo do módulo*/
	public function module($moduleName)
	{
		$lang = $this->selectAutoLang();
		$moduleName = 'Modules/'.$moduleName.'/Langs/'.$lang.'.json';
		$this->import($moduleName,true);
	}
	
	/*Seleciona automáticamente o idioma de acordo com as configurações do usuário*/
	private function selectAutoLang(){
		if(Auth::check()){
			$lang = Auth::user('lang');
		}elseif (isset($_COOKIE['np_front_lang'])) {
			$lang = $_COOKIE['np_front_lang'];
		}else{
			$lang = NP_LANG;
		}
        return $lang;
	}
}
