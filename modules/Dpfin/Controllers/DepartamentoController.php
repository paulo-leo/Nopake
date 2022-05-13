<?php 
namespace Modules\Dpfin\Controllers;

use Nopadi\MVC\Controller;

use Modules\Fin\Controllers\GroupController;

class DepartamentoController extends Controller
{
    /*Função para exibição da listagem de funcionarios*/
    public function index()
	{
    
	 $dp = new GroupController;
	 return $dp->index(4);
        
    }	
	public function create()
	{
    
	 $dp = new GroupController;
	 return $dp->create(4);
        
    }	
} 
