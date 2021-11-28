<?php
namespace Modules\Ecommerce\Models;

use Nopadi\MVC\Model;

class FaqModel extends Model
    {
	  /*Nome da tabela*/
	  protected $table = "so_faqs";
	  
	   /*Prover o acesso estático ao modelo*/
	  public static function model()
	  {
		return new FaqModel();
	  }   
    }

