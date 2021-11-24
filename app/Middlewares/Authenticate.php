<?php

namespace App\Middlewares;

use Nopadi\Http\Auth;
use Nopadi\Http\URI;
use Nopadi\Http\Middleware;

class Authenticate extends Middleware
    {
	 public function handle($role)
	 {
		 //organization
		 $role = is_null($role) ? null : $role;
		 if(!Auth::check($role))
		 {
           $this->setHistUrl();
		   $this->redirect('login');
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



