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

class LoginJWTController extends Controller
{
  
   /*Realiza o cadastro de um novo usuário*/
   
   
   public function Register()
   {
	   $request = new Request;
	   $jwt = new JWT;
	   if($request->getHeader('app-key') == NP_KEY_API){
		   
		   $name = $request->getString('name','5:100');
		   $email = $request->getEmail('email');
		   $password = $request->getString('password','4:100');
		   $lang = $request->get('lang',NP_LANG);
		   $theme = $request->get('theme','black');
		   $accept_terms = $request->getOn('accept_terms');
		   $email_marketing = $request->getOn('email_marketing');
		   
       
		   $data = array(
		    'name'=>$name,
			'email'=>$email,
			'password'=>$password,
			'lang'=>$lang,
			'theme'=>$theme,
			'accept_terms'=>$accept_terms,
			'email_marketing'=>$email_marketing
		   );
		   
		   $user_id = Auth::create($data);
		   
		   if($user_id){
			  $jwt->setCode(201);
			  $jwt->setMessage('User_registered_successfully');
			  $data = array(
			  'user_id'=>$user_id
			  );
			  return $jwt->response($data,true);  
			   
		   }else{
			 $jwt->setCode(401);
	          return $jwt->response(['errors'=>Auth::status()],true);  
		   }
		   
		   
	   }else{
		   $jwt->setCode(403);
	       return $jwt->response(['errors'=>['Token da API inválido!']],true);
	   }
   }
	
   /*Realiza o login e retorna um token JWT*/
   public function Login()
   {
	 $request = new Request;
	 $jwt = new JWT;
	 
	 if($request->getHeader('app-key') == NP_KEY_API){
	 
	 $login = $request->getEmail('login');
	 $password = $request->getString('password','4:100');
     
	 $request->setMessages([
	  'login'=>'E-mail inválido!',
	  'password'=>'Senha inválido!'
	 ]);
	 
	 if($request->checkMessages()){
		 
		$login = Auth::loginJWT($login,$password);
		 
		if($login){
			
			return $jwt->login($login,true);
			
		}else{
			$jwt->setCode(401);
			return $jwt->response(['errors'=>['Usuário ou senha inválido!']],true);
		}
		 
	 }else{
		 $jwt->setCode(401);
		 return $jwt->response(['errors'=>$request->getMessages()],true);
	 }
	 }else{
		 $jwt->setCode(403);
	     return $jwt->response(['errors'=>['Token da API inválido!']],true);
	 }
   }
} 





