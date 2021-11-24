<?php
namespace App\Providers;

use Nopadi\Http\Auth;
use Nopadi\Support\ServiceProvider;

class CSRFServiceProvider extends ServiceProvider{
	
	 private $tokenName = 'np_csrf_token';
	 private $tokenNamePost = '_token';
	
	 /*Inicia o serviço*/
	 public function boot()
	 { 
	   if(!is_api())
	   {
		 $this->tokenCreate();
		 //Valida o token no metodo post
		 if(is_method('post') && !is_url('/'))
		 {
			
			$token = isset($_POST[$this->tokenNamePost]) ? $_POST[$this->tokenNamePost] : null;
			$csrf_token = $_SESSION[$this->tokenName];
			
			if($token == $csrf_token)
			{
			    unset($_POST[$this->tokenNamePost]);
			}else
			{ 
			    ServiceProvider::status('token.invalid',text(':invalid_token_csrf'));
			}
	     }
	   }
	 }
	 /*Gera o token*/
	 private function tokenCreate()
	 {

		   if(Auth::check())
		   {
			if(!isset($_SESSION[$this->tokenName]))
				 $_SESSION[$this->tokenName] = md5(date('Y-m-d-H').Auth::user()->id);
			
		   }else
		   {
			   if(!isset($_SESSION[$this->tokenName]))
			     $_SESSION[$this->tokenName] = md5(date('Y-m-d-H')); 
		    }
	 }
}
