<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\FS\Json;
use Nopadi\FS\ReadArray;
use Nopadi\Http\Auth;
use Nopadi\Http\Request;
use Nopadi\Support\ACL;
use Nopadi\MVC\Controller;

class PermissionController extends Controller
{

   private $fileDir = 'config/access/permissions.json';
   private $instance;
   private $permissions = array();
   private $msg_error;
   
   public function __construct()
   {
	   $this->instance = new Json($this->fileDir);
	   $this->permissions = $this->instance->gets();  
   }
   /*Remove os acessos vinculados aos rules pela key*/
   private function removeAccess($permission)
   {
	 
	  $file = 'config/access/access.php';
	  $file = new ReadArray($file);
	  $keys = $file->getKeys();
      $r = null;
	  
	  foreach($keys as $role)
	  {
		$permissions = $file->get($role);
		$position = array_search($permission,$permissions);
		$i = 0;

		if(is_numeric($position))
		{
		  	unset($permissions[$position]);
			$file->set($role,$permissions);
			$file->save(true);
			$i++;
		}
	  }
	  return $i;
   } 

   public function getPermissions()
   { 
	$data = array('permissions'=>$this->permissions);

	 return view('@Painel/Views/settings/permission',$data);
   }
   
   /*Remove uma permissão especifica*/
   public function remove()
   {
	   
	   $request = new Request;
	   $key = $request->get('key');
	 
	   if($this->instance->exists_key($key))
	   {
		  $this->instance->del($key);
		  $this->instance->save(true);
		  $this->removeAccess($key);
		  
		  return alert("Permissão excluída com sucesso e refletida nas funções","success");
		  
	   }else{
		  return alert("Não foi possível excluir essa permissão.","danger"); 
	   }
   }
   
   public function create()
   {
	  return view('@Painel/Views/settings/permission-create');
   }
} 