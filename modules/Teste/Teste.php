<?php 
namespace Modules\Teste; 

use Nopadi\MVC\Module;
use Nopadi\Http\Route;
use Nopadi\Http\Send;

class Teste extends Module
{
	/*Este é método principal para execução do módulo enquanto ativo*/
	public function main()
    {
		Route::get('teste/auto',function(){
			
			Send::email([
			  'email'=>'pauloleonardo.rio@gmail.com',
			  'message'=>'Este é um e-mail automátivo de teste enviado em: '.NP_DATETIME,
			  'name'=>'Teste System',
			  'subject'=>'E-mail automático']);
			
		});
    }
} 
