<?php 
namespace App\Models\Schemas; 

use Nopadi\Base\CreateSchema;

class cats extends CreateSchema
{
 
   public function create()
   {
     $this->createTable('cats');
	 $this->pk();
	 $this->text('type',40);
     $this->text('name',150);
	 $this->text('description',250);
	 $this->text('uri',200);
	 $this->int('cat_id',20);
	
     return $this->execute();
   } 
 
   public function modify()
   {
     $this->defPK('users');
     return $this->execute();
   }
 
}
