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
	
	
	public function main()
    {
		
    }
	
	public function active()
    {
        
    }
	
	public function disabled()
    {
        
    }
	
}
