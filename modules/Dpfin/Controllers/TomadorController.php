<?php 
namespace Modules\Dpfin\Controllers; 


use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Base\DB;
use Nopadi\Http\Send;
use Nopadi\Http\Request;
use Nopadi\Http\Param;
use Nopadi\MVC\Controller;

use Modules\Fin\Models\ProviderModel;
use Modules\Fin\Controllers\ProviderController;

class TomadorController extends Controller
{
    /*Função para exibição da listagem de tomadores*/
    public function index()
	{
        if(access('dp_fin_tomador')){

        $list = new ProviderModel; 

         $list = $list->where('taker_status',1)    
        ->paginate(20);
		
        $data = array(
            'list'=>$list
        );

        return view('@Dpfin/Views/tomador/index', $data);
        }else{
			return view('@Painel/Views/404');
		}
    }	

    public function diariaTeste()
    {
        return view('@Dpfin/Views/Tomador/diaria');
    }


    /*Função que retorna a view de edição*/
    public function edit()
    {    
        if(access('dp_fin_tomador_editar')){
			$c = new ProviderController;
           return $c->edit();
		}else{
			return view('@Painel/Views/404');
		}    
        
    }

    /*Função que retorna a view de visualiação*/
    public function show()
    {
		if(access('dp_fin_tomador')){
        $c = new ProviderController;
        return $c->show();
		}else{
			return view('@Painel/Views/404');
		}
    }
} 
