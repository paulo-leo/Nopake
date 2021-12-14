<?php
use Nopadi\Http\Auth;
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

  $password = '123456';
  $id = 1;
  $x = Auth::passwordUpdateManual($password, $id);
  return $x;
});







