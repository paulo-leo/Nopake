<?php

use Nopadi\Http\Route;
use Nopadi\FS\Json;

/****************************************************************
 ******** Nopadi - Desenvolvimento web progressivo***************
 ********* Arquivo de rotas para API******************************
 *****************************************************************/

use Nopadi\Http\JWT;
use Nopadi\Http\Auth;
use Nopadi\Http\Request;
use Nopadi\Base\Schema;
use Nopadi\Http\URI;

Route::access();

Route::get('my-frame',function(){
	
	if(isset($_GET['msg'])){
		echo $_GET['msg'];
	}
	
	echo '<form>
	       <input type="text" name="msg" />
		   <input type="submit">
	     </form>';
		 
	
});


Route::get('frame',function(){
	
	echo '<iframe frameborder="0" src="'.url('my-frame').'"></iframe>';
	
});



function _utf8_decode($string)
{
  $tmp = $string;
  $count = 0;
  while (mb_detect_encoding($tmp)=="UTF-8")
  {
    $tmp = utf8_decode($tmp);
    $count++;
  }
 
  for ($i = 0; $i < $count-1 ; $i++)
  {
    $string = utf8_decode($string);
   
  }
  return $string;
 
}

Route::get('json/{string}',function(){
	
	
	
	var_dump(Auth::sector());
	
	
});












