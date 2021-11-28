<?php

use Nopadi\Base\DB;
use Nopadi\Http\Route;
use Nopadi\Http\Request;

/*******************************************************
 ******** Nopake - Desenvolvimento web progressivo *****
 ******** Arquivo de rotas principal (web) *************
*******************************************************/

Route::get('*',function(){ return view('404'); });
Route::get('/',function(){ return view('welcome'); });



Route::get('teste',function(){
	
  $request = new Request;
  $page = $request->get('page');
  $search = $request->get('search');

  $datas = DB::table('users');
  
if(strlen(trim($search)) > 1)
  {
    $sql = "SELECT id,name FROM users WHERE name LIKE '%{$search}'" ;
  }else{
    $sql = "SELECT id,name FROM users";
  }

  $datas = $datas->firstQuery($sql);
  $datas = $datas->paginate();

  $data = null;

  foreach ($datas->results as $values) 
  {
    extract($values);
    $data[] = array("id"=>$id, "text"=>'['.$id.'] '.$name);
  }

  $data[] = array("paginate"=>true);
  
  return json($data);


});







