<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\Http\URI;
use Nopadi\Http\Send;
use Nopadi\Http\Auth;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Nopadi\Model\UserModel;
use Nopadi\Model\CodeModel;

class RegisterController extends Controller
{
	
   /*Exibe o formulário de login*/
   public function showRegistrationForm()
   {
	    return view("@Painel/Views/register",['logout'=>false]); 
	   /*
	      if(NP_NEW_MEMBERS == 'on'){
			 return view("@Painel/Views/register",['logout'=>false]);  
		  }else{
			  return view("404"); 
		  }
	   */   
   }
   
   
   public function registerNowCheck()
   {
	   $token = Param::get('token');
	   $msg = "Token inválido";
	   if($token)
	   {
		   $values = CodeModel::model()->find(['token'=>$token]);
		   
		   if($values)
		   {
			 $email = $values->email; 
             return view("@Painel/Views/register",['email'=>$email]);			 
		   }else{
			   hello($msg);
		   }
	   }else{
		   hello($msg);
	   }
	   
	   
	   
   }
   
   public function codeCheck()
   {
	  $request = new Request;
	  $code = $request->getInt('code');
	  $email = $request->getEmail('email'); 
	  if($email && $code){
		  
	  
	  $values = array('email'=>$email,'code'=>$code);
	  $values = CodeModel::model()->find($values);
	   
	  if($values)
	  {
		  $url = url('register/'.$values->token);
		  hello($url);
		  
	  }else{
		   hello(404);
	  }
	  }else{
		   hello(404);
	  }
   }
   
   public function registerNow()
   {
	  //showRegistrationFormCode 
	  $check = false;
	  
	  if($check){
		  
		 return view("@Painel/Views/register",['email'=>""]); 
		 
	  }else{
		  
		 return view("@Painel/Views/code"); 
		  
	  }
	   
   }
   
   
   public function register()
   {
	  $request = new Request;
	  $msg = null;
	  $register = null;
	  
	  $name = $request->get('name');
	  $surname = $request->get('surname');
	  
	  $password = $request->get('password1');
	  $password2 = $request->get('password2');
	  $email = $request->getEmail('email');
	  $lang = $request->get('lang',NP_LANG);
	  $accept_terms = $request->get('accept_terms','off');  
	  $email_marketing = $request->get('email_marketing','off');
	  
	  if($password == $password2)
	  {
		  $name = $name.' '.$surname;
		  
		  $values = array('name' => $name,
						'lang' => $lang,
						'accept_terms'=>$accept_terms,
						'email' =>$email,
						'email_marketing'=>$email_marketing,
						'$accept_terms'=>$accept_terms,
						'password' =>$password);
	  
	     $register = Auth::create($values);
		 $msg = Auth::status();
		  
	  }else{

		 $msg = "passwords_do_not_match"; 

	  }  
	  
	  if($register){ 

		  return alert($register,'success'); 

		}else{

		return alert($msg,'danger');

	  }
   }
   public function showRegistrationFormCode()
   {
	  return view("@Painel/Views/code",['logout'=>false]); 
   }
   
   public function code()
   {
	 sleep(3);
	 $request = new Request;
	 $email = $request->getEmail('email');
	 
	 if($email){
		   $values = array('email'=>$email);
	 
	       $exists = UserModel::model()->exists($values);
	      
		   if($exists){

			$code = date('sdmy');
			$token = md5($code.$email);
			$link = new URI();
            $link = $link->base();		
			$link_code = $link.'register/'.$token;
			$link_code = "<a href='{$link_code}'>Confirmar autenticidade</a>";
			
			$subject = "Código para registro";
			$name = $email;
			$html = "<div style='text-align:center'>Código de confirmação<h1 style='border:1px solid green'>{$code}</h1>Sua conta no(a) ".NP_NAME." está quase pronta. Para ativá-la, por favor copie e cole o código na tela solicitada ou clique no link abaixo<br><br>{$link_code}</p></div>";
			$text = "{$subject}: {$code}";
			  
		     $send = Send::email([
	              'email'=>$email,
	              'name'=>$name,
	              'title'=>$subject,
	              'text'=>$text,
	              'html'=>$html]);
				
			if($send){
				
				$values = array(
				  'code'=>$code,
				  'token'=>$token,
				  'email'=>$email
				);
				
				$insert = CodeModel::model()->insert($values);
				hello(200);
				
				
				
			}else{
				
				hello('Ops! Por algum motivo não foi possível confirmar o seu e-mail.','danger');
			}	
				
			   
		   }else{
			   
			   hello('Já existe um usuário registrado com este e-mail.','danger');
		   }
		  
		   
	 }else{
		 
	 }

	 
   }
} 