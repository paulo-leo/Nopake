<?php

namespace App\Middlewares;

use Nopadi\Http\Middleware;
use Nopadi\Http\Request;

    class CORS extends Middleware
    {
	  public function handle($role='*')
	  { 
		header("Access-Control-Allow-Origin: {$role}");
		header("Status: 200 ok");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Max-Age: 1000");
		header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding, App-Key");
		header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
	}
}