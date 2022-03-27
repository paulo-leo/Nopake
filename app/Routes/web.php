<?php

use Nopadi\Http\Route;

/*******************************************************
 ******** Nopake - Desenvolvimento web progressivo *****
 ******** Arquivo de rotas principal (web) *************
*******************************************************/

Route::get('*',function(){ return view('404'); });
Route::get('/',function(){ return view('welcome'); });

Route::get('teste',function(){ 
    return view('teste'); 
});
