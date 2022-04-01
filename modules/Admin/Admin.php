<?php 
namespace Modules\Admin; 

use Nopadi\MVC\Module;
use Nopadi\Http\Route;


class Admin extends Module
{
	/*Este é método principal para execução do módulo enquanto ativo*/
	public function main()
    {
	
	   Route::get('admin/users',function(){
		   
		      
			  return view('@Admin/Views/users/index');
		   
		   
	   });
	
    }

	/*Ativa o módulo*/
	public function on()
	{
          
	}

	/*Desativa o módulo*/
	public function off()
	{
        
	}
} 
