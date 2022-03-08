<?php
/*
*Classe para criar recursos padronizados para as rotas.
*Autor: Paulo Leonardo da Silva Cassimiro
*/
namespace Nopadi\MVC;

use Nopadi\FS\Json;
use Nopadi\Http\Route;

class Module
{
	final public static function start()
	{
		 Route::module('Painel','Module');
		 
		 $json = new Json('config/app/modules.json');
	     $modules = $json->gets();
		 
		 foreach($modules as $key=>$value)
		 {
			 extract($value);
			 if($key != 'Painel')
			 {
			   if($status == 'active')
			    {
				   Route::module($key);
			    }
			 }
		  }
	 }
	
	/*Este é método principal para execução do módulo enquanto ativo*/
	public function main()
    {
		
    }

	public function on()
	{

	}
	
	public function off()
	{

	}

	/*Este método será executado na ativação do módulo*/
	public function active()
    {
        $this->on();
    }
	
	/*Este método será executado na desativação do módulo*/
	public function disabled()
    {
        $this->off();
    }
	
	/*Este método será executado na atualização do módulo*/
	public function update()
    {
        
    }
	
}
