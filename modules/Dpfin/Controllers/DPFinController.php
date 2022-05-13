<?php 
namespace Modules\Dpfin\Controllers; 

use Nopadi\Base\DB;
use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Modules\Fin\Controllers\TitleController;
use Modules\Fin\Controllers\BrancheController;
use Modules\Fin\Models\ProviderModel;

class DPFinController extends Controller
{
	public function index()
	{
		
	}
	
	public function showProviders()
	{
		 $request = new Request;
	  $type = $request->getInt('type');
	   
      $filter = new Request();
	  
	   
	   if($type == 1){
		   $type = array(['type',1],['type',4],['type',5],['type',7]);
	   }elseif($type == 2){
		   $type = array(['type',2],['type',4],['type',6],['type',7]);
	   }elseif($type == 3){
		   $type = array(['type',3],['type',5],['type',6],['type',7]);
	   }else{
		    $type = array(['type',0]);
	   }
	  
	  $list = ProviderModel::model()
	  ->whereOr($type)
	  ->orderBy('code asc')
	  ->paginate(30);

	  

       $data = array('list'=>$list,'type'=>$type);
       return view('@Fin/Views/providers/index',$data);
	}
} 






