<?php 
/*
Autor: Paulo Leonardo
*/
namespace Modules\Painel\Controllers; 

use Nopadi\Base\DB;
use Nopadi\Http\Auth;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;

class RecordController extends Controller
{
   public function executeRecord()
   {
       $request = new Request;
	   $query = $request->get('query');
	   
	   $query = DB::sql($query);
	   
	   if($query){
		   $arr = array();
		   $arr['status'] = 200;
		   $arr['results'] = $query;
		   return json($arr);
	   }else{
		 return json(['status'=>404]);
	   }
	   
   }
} 





