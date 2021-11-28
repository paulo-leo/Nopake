<?php 
namespace App\Models\Schemas; 

use Nopadi\Base\CreateSchema;

class users extends CreateSchema
{
 
   public function create()
   {
     $this->createTable('users');
	 $this->pk();
     $this->text('name',50);
	 $this->text('email',70);
	 $this->text('lang',100);
	 $this->text('status',15);
	 $this->text('password',62);
	 $this->datetime('created_in',62);
	
     return $this->execute();
	 
   } 
 
   public function modify()
   {
	 $this->defPK('users');
     return $this->execute();
   }
 
}
