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
   
  $r = new Request;  
  $v = new FormValidate($r->all());

  $v->validate([
     'name'=>'number|required|min:3|max:4',
     'namex'=>'number|required|min:3|max:4'
   ]);

 var_dump($v->getErrors());

}


Route::get('teste','teste');