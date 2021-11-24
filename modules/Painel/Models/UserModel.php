<?php
namespace Modules\Painel\Models;

use Nopadi\MVC\Model;

class UserModel extends Model
    {
	  /*Prover o acesso estático ao modelo*/
	  public static function model()
	  {
		return new UserModel();
	  } 	
    }

