<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\MVC\Controller;

class ForgotPasswordController extends Controller
{
	
   /*Valida o token para alteração da senha*/
   public function showLinkRequestForm()
   {
       return view("@Painel/Views/reset");
   } 
   
   /*Gera o token do usuário e o envia para o e-mail correspondente*/
	public function sendResetLinkEmail()
	{
		
	  $email = filter_input(INPUT_POST,'email');
	  filter_var($email, FILTER_VALIDATE_EMAIL);
	  
	  if($email){
		  
		  $key = Auth::createTokenByEmail();
		  
		  if($key){
			  
			$name = $key['name'];
			$token = $key['token'];
			
            $link = new URI();
            $link = $link->base();		
			$link_token = $link.'password/reset/'.$token;
			  
		  $send = Send::email([
	              'email'=>$email,
	              'name'=>$name,
	              'title'=>text(':redefine_password'),
	              'text'=>text(':email_msg_alternative').':'.$link_token,
	              'html'=>'<div><h3>'.text(':redefine_password').'</h3><p>'.text(':hello').' <b>'.$name.'</b>! '.text(':email_msg_resert_password').'</p>
				  <a href="'.$link_token.'">'.text(':redefine_password').'</a></div>'
	       ]);
		   
		   $msg_info = text(':hello').'! '.text(':email_msg_send_success');
		   
		   if($send){
			   hello($msg_info,'info');
		   }else{
			   hello('Falha ao enviar e-mail. Verifique as configurações do servidor SMTP com o administrador do sistema ou com a equipe de suporte de TI.','danger');
		   } 
			  
		  }else hello(':email_not_found','warning');
		  
	  }else hello(':invalid_email','danger');
	}
	
	/*Exibe um formulário para alteração da senha após validar o token do usuário obtido via URL*/
	public function showResetForm()
	{

	   $token = Param::get('token');
       $token = Auth::checkToken($token); 
      
	   return view("@Painel/Views/reset-form",['token'=>$token]);
	   
	}
	
	/*Faz a alteração da senha do usuário*/
	public function reset()
	{
	   
	  $password = Auth::passwordUpdate();
      if($password) hello(':password_update_success','success');
	  else hello(':password_update_error','success','danger');
	   
	}
} 





