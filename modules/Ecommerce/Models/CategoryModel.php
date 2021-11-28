<?php
namespace Modules\Ecommerce\Models;

use Nopadi\MVC\Model;

class CategoryModel extends Model
    {
	  /*Nome da tabela*/
	  protected $table = "so_categories";
	  
	   /*Prover o acesso estático ao modelo*/
	  public static function model()
	  {
		return new CategoryModel();
	  } 
	  
	  /*Retorna  as categorias pelo tipo*/
	  public function getCategories($type)
	  {
       
		 $c = $this->select(['id','name'])
		 ->where('type',$type)
		 ->get();

		 return id_value($c);

	  }    

	  

    }

