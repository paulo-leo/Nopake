<?php
namespace Nopadi\Model;

use Nopadi\MVC\Model;

class CodeModel extends Model
    {
	  /*Prover o acesso estático ao modelo*/
	  public static function model()
	  {
		return new CodeModel();
	  } 	
    }
