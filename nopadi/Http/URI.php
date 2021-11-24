<?php

namespace Nopadi\Http;

class URI
{

	/**
	 * $protocolo
	 * @var string | $protocolo
	 * @access private
	 */
	static private $protocolo;
	private $local = false;

	/**
	 * $host
	 * @var string | $host
	 * @access private
	 */
	static private $host;

	/**
	 * $scriptName
	 * @var string | $scriptName
	 * @access private
	 */
	static private $scriptName;

	/**
	 * $finalBase
	 * @var string | $finalBase
	 * @access private
	 */
	static private $finalBase;


	/**
	 * protected function Protocolo()
	 * ----------------------------------------------
	 * 			  Obtém o protocolo da url
	 * ----------------------------------------------
	 * @return string | Ex: http://... - https://...
	 * @access protected
	 */
	protected function Protocolo()
	{
		$https = ((!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off')) ? true : false; 
		 
		if ($https) {
			self::$protocolo = 'https://'; //Atribui o valor https
			
		} else {
			self::$protocolo = 'http://'; //Atribui o valor http
		}

		/**
		 * Retorna o protocolo em formato string
		 * @var string
		 */
		return self::$protocolo;
	}

	/**
	 * protected function Host()
	 * ----------------------------------------------
	 * 			  Obtém o host principal
	 * ----------------------------------------------
	 * @return string | Ex: www.example.com.br
	 * @access protected
	 */
	protected function Host()
	{
		self::$host = $_SERVER['HTTP_HOST']; //Atribui o valor www.example.com.br

		/**
		 * Retorna o host em formato string
		 * @var string
		 */
		return self::$host;
	}

	/**
	 * protected function scriptName()
	 * ----------------------------------------------
	 * Obtém o script name do host após a primeira barra
	 * ----------------------------------------------
	 * @return string | Ex: .../dir/index.php
	 * @access protected
	 */
	protected function scriptName()
	{
		/**
		 * $scr
		 * Atribui o valor do SCRIPT_NAME em uma
		 * variável $scr e utiliza-se a função dirname()
		 * para remover qualquer nome de arquivo .html, .php, etc...
		 * @var string
		 */
		$scr = dirname($_SERVER['SCRIPT_NAME']);

		/**
		 * Faz a contagem de barras que contém a url principal
		 * o objetivo aqui é pegar o nível de pasta onde hospeda-se o diretório
		 * caso ele exista.
		 */
		if (!empty($scr) || substr_count($scr, '/') > 1) {
			self::$scriptName = $scr . '/'; //atribui o valor do diretório com uma "/" na sequência
		} else {
			self::$scriptName = ''; //atribui um valor vazio
		}

		/**
		 * Retorna o scriptName em formato string
		 * @var string
		 */
		return self::$scriptName;
	}

	/**
	 * public function base()
	 * ----------------------------------------------
	 * 			Monta a url base e retorna
	 * ----------------------------------------------
	 * @return [type] [description]
	 * @access public
	 */
	public function base()
	{
		//Concatena os valores
		$base = self::$finalBase = self::Protocolo() . self::Host() . self::scriptName();

		/**
		 * Retorna toda a url construida em formato string
		 * @var string
		 */

		if (self::$protocolo . $_SERVER['HTTP_HOST'] == self::$protocolo . 'localhost:8080') {
			$base = self::$protocolo . 'localhost:8080/';
			$this->local = true;
		}

		if (self::$protocolo . $_SERVER['HTTP_HOST'] == self::$protocolo . 'localhost:8000') {
			$base = self::$protocolo . 'localhost:8000/';
			$this->local = true;
		}

		$base = (substr($base, -2) == '//') ? substr($base, 0, -1) : $base;

		return $base;
	}
	public function uri()
	{
		//Concatena os valores
		self::$finalBase = self::Protocolo() . self::Host() . $_SERVER['REQUEST_URI'];

		/**
		 * Retorna toda a url construida em formato string
		 * @var string
		 */
		return self::$finalBase;
	}
	public function local($path)
	{
		$this->base();

		if ($this->local == false) {
			$path = '../' . $path;
			$path = str_ireplace('../../','../',$path);
			$path = str_ireplace('../../../','../',$path);
			$path = str_ireplace('../../../../','../',$path);
		}
		
		
		return $path;
	}
	public function asset($path)
	{
		$url = $this->base();

		//$uri.'public/'.$path;

		if ($this->local == false) {

			$path = $url . $path;
		} else {

			$path = $url . 'public/' . $path;
		}

		return $path;
	}
}
