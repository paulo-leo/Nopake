<?php 
/*
Autor: Paulo Leonardo
*/
namespace Modules\Painel\Controllers; 

use Nopadi\Http\Auth;
use Nopadi\Http\JWT;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;

class LoginController extends Controller
{
	
   /*Exibe o formulário de login*/
   public function showLoginForm()
   {
	  if(!Auth::check())
	      return view("@Painel/Views/login",['logout'=>false]); 
	  else to_url('dashboard');	
   }
   
   /*Faz o login do usuário*/
   public function login()
   {
        Auth::post();
	    return Auth::status();
   }
   
   /*Desloga o usuário*/
   public function logout()
   {
     if(Auth::destroy())
	 return view("@Painel/Views/login",['logout'=>true]); 
   }
   
   public function teste(){
	   
	   return view("@Painel/Views/users/form");	   
	   
    }
	/*Faz login via JWT*/
	public function jwtLogin()
	{
		$request = new Request;
		
		$login = $request->get('login');
		$password = $request->get('password');
		
		$login = Auth:: loginJWT($login,$password);
		
		$jwt = new JWT;
		
		if($login)
		{
			$jwt->login($login,true);
			
		}else{
			
			$jwt->response([
			'code'=>401,
			'message'=>'Login ou senha inválida.'
			]);
		}
	}
} 





