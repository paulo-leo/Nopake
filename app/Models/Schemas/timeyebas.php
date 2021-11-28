<?php 
namespace App\Models\Schemas;

use Nopadi\Base\CreateSchema;

class timeyebas extends CreateSchema
{
 
   public function create()
   {
	   
	$this->createTable('time_yebas');
	$this->pk();
	$this->fk('yeba_id','yebas');
	
	$this->text('every_day',15);
	$this->time('h_init');
	$this->time('h_end');
	$this->int('total',10);
	
    return $this->execute();

   } 
 
   public function modify()
   {
	  $this->dropTable('time_yebas');
      return $this->execute();
   }
 
}
