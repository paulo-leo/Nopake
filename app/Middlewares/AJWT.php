<?php

namespace App\Middlewares;

use Nopadi\Http\Auth;
use Nopadi\Http\JWT;
use Nopadi\Http\Middleware;

class AJWT extends Middleware
    {
	 public function handle($role)
	 {
		$jwt = new JWT;
		$GLOBALS['ajwt'] = array();
		
		if(!$jwt->auth())
		{
			$jwt->response();
            exit;
		}else{
          
		  $GLOBALS['ajwt'] = $jwt->all();
		}
	 }
}