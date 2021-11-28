<?php
namespace Modules\Ecommerce\Models;

use Nopadi\MVC\Model;

class ProductModel extends Model
    {
	  /*Nome da tabela*/
	  protected $table = "so_products";
	  
	   /*Prover o acesso estático ao modelo*/
	  public static function model()
	  {
		return new ProductModel();
	  }   
    }

