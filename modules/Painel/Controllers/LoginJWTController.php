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
   /*Altera a senha do usuário via JWT*/
   public function updatePasswordJWT()
   {
	       $jwt = new JWT;
	       $request = new Request();
	       $user_id = $GLOBALS['ajwt']['id'];
		   $pass = $request->get('password');
		   $pass1 = $request->get('password-1');
		   $pass2 = $request->get('password-2');
		   $password = true;

           if(NP_STRONG_PASSWORD == 'on')
		   {
			 $password = preg_match('/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/',$pass1) ? true : false;
		   }

           if($password){
		   if(Auth::checkPassword($pass,$user_id)){
			 if($pass1 == $pass2 && strlen($pass1) > 5){
			  if($pass1 != $pass){
			    if(Auth::passwordUpdateManual($pass1,$user_id)){ 
				
				  $jwt->setCode(201);
			      $jwt->setMessage(':password_update_success');	
				  
				}else{
					
                  $jwt->setCode(422);
			      $jwt->setMessage(':password_update_error');
				  
				} 
			  }else{ 
			  
			      $jwt->setCode(422);
			      $jwt->setMessage(':equal_password');
			  }  
		     }else{ 
			 
			 $jwt->setCode(422);
			 $jwt->setMessage(':passwords_do_not_match');	
			 
			 }
		  }else{ 
		          $jwt->setCode(422);
			      $jwt->setMessage(':invalid_password');	
		   }
		   }else{
			   
			   $jwt->setCode(422);
			   $jwt->setMessage(':invalid_password_strong');	
		   }	   
       
      return $jwt->response(null,true);	
   }
    
  
   /*Realiza o cadastro de um novo usuário*/
   public function register($callback=null)
   {
	   $request = new Request;
	   $jwt = new JWT;
	   if($request->getHeader('App-Key') == NP_KEY_API && NP_ACTIVE_API == 'on'){
		   
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
			  
			  if(is_callable($callback))
			  {
				 $data = call_user_func($callback, $data); 	
			  }
			  
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
   public function login($login=null,$callback=null,$r=false)
   {
	 $request = new Request;
	 $jwt = new JWT;
	 
	 if($request->getHeader('App-Key') == NP_KEY_API && NP_ACTIVE_API == 'on'){
	 
	 
	 $login = is_null($login) ? $request->getEmail('login') : $login;
	 
	 
	 
	 $password = $request->getString('password','4:100');
     
	 $request->setMessages([
	  'login'=>'Login inválido!',
	  'password'=>'Senha inválido!'
	 ]);
	 
	 if($request->checkMessages()){
		 
		$login = Auth::loginJWT($login,$password);
		 
		if($login){
			
			$login['image'] = url($login['image']);
			
			if(is_callable($callback))
			{
				$login = call_user_func($callback, $login); 	
			}
			
		
			return $jwt->login($login,$r);
			
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
