<?php
namespace App\Models;

use Nopadi\MVC\Model;

class TokenModel extends Model
    {
	  /*Prover o acesso estático ao modelo*/
	  public static function model()
	  {
		return new TokenModel();
	  } 	
    }
