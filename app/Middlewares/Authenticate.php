<?php

namespace App\Middlewares;

use Nopadi\Http\Auth;
use Nopadi\Http\URI;
use Nopadi\Http\Middleware;

class Authenticate extends Middleware
    {
	 public function handle($role)
	 {
		 $redirect = 'login';

         if(substr($role,0,1) == '@' && strlen($role) >= 2)
		 {
            $args = explode('|',substr($role,1));
			$redirect = $args[0];
			$role = isset($args[1]) ? $args[1] : null;
		 }
		
		 $role = is_null($role) ? null : $role;
		 if(!Auth::check($role))
		 {
           $this->setHistUrl();
		   $this->redirect($redirect);
		 }else{
			 unset($_SESSION['np_route_auth_login']);
		 } 
	 }

	 /*Salva o histórico da ultima página acessada*/
	 private function setHistUrl()
	 {
		 $uri = new URI;
         $url = $uri->uri();
		 if(!isset($_SESSION)) session_start();
		 
		 $_SESSION['np_route_auth_login'] = $url;
	 }
}