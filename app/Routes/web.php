<?php
use Nopadi\Http\Auth;
use Nopadi\Base\DB;
use Nopadi\Base\Table;
use Nopadi\Http\Route;
use Nopadi\Http\Request;
use Nopadi\Http\FormValidate;

/*******************************************************
 ******** Nopake - Desenvolvimento web progressivo *****
 ******** Arquivo de rotas principal (web) *************
*******************************************************/

Route::get('*',function(){ return view('404'); });
Route::get('/',function(){ return view('welcome'); });


function teste(){
  
  
  $r = response([
    'url'=>'https://pokeapi.co/api/v2/ability',
    'method'=>'get','array'=>true]);

  var_dump($r->results);
}


Route::get('teste','teste');