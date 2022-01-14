<?php
use Nopadi\Http\Auth;
use Nopadi\Base\DB;
use Nopadi\Base\Table;
use Nopadi\Http\Route;
use Nopadi\Http\Request;

/*******************************************************
 ******** Nopake - Desenvolvimento web progressivo *****
 ******** Arquivo de rotas principal (web) *************
*******************************************************/

Route::get('*',function(){ return view('404'); });
Route::get('/',function(){ return view('welcome'); });


function teste(){

  $table = new Table;

  $tables = [
     'tabela1'=>[
        'id|primary_key',
        'nome|string|size:20',
        'telefone|number|size:11|null|default:219890444',
        'criado_em|timestamp'],
     'tabela2'=>[
        'id|primary_key',
        'nome|string|size:20',
        'preco|money',
        'tabela1_id:tabela1:id|fk']
    ];

   
    $table->create($tables);

    //$table->drop(['tabela2','tabela1']);
}


Route::get('teste','teste');