<?php 
namespace App\Models\Schemas;

use Nopadi\Base\CreateSchema;

class yebas extends CreateSchema
{
 
   public function create()
   {
	   
	$this->createTable('yebas');
	$this->pk();
	$this->fk('user_id','users');
	$this->text('name',100);
	$this->text('uri',100);
    $this->text('description',250);
    $this->text('type',10);
	$this->text('status',10);
	$this->text('parents',60);
	$this->text('city',80);
	$this->text('meeting_point',100);
	$this->text('gooqle_map_html',200);
	$this->int('group_booking',6);
	$this->money('price');
	$this->money('local_price');
	
    return $this->execute();

   } 
 
   public function modify()
   {
	  $this->dropTable('yebas');
      return $this->execute();
   }
 
}
