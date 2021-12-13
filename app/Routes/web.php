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



Route::post('api/teste',function(){


  $r = new Request;
  return json($r->all());
  
});







