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


Route::get('x',function(){
	
    echo NP_NAME;
	
});


Route::get('x',function(){
	
	var_dump(errors());
	
});

Route::get('/teste',function(){
	
   $route = url('teste');
   $token = csrf_token();
   
   $form = "<form action='{$route}' method='post'>";
   $form .= "<input type='hidden' name='_token' value='{$token}'>";
   $form .= "<input type='text' name='name' value='Paulo'>";
   $form .= "<input type='text' name='type' value='Produtos'>";
   $form .= "<input type='submit'>";
   
   return $form;
	
});

Route::post('/teste',function(){
	
	//$_POST['carro'] = 10;
	$request = new Request;
	
	$check = array(
	  'carro'=>array('type'=>'number','dafault'=>'100'),
	  'nome'=>array('min'=>5,'max'=>10,'type'=>'string','reg'=>'[bdbf]{10}')
	);
	
    if($request->check($check)) 
		return 'Validado';
    else 
		return 'Inválido';	
	
	
});







