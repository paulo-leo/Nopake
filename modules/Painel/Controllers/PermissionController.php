<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\FS\Json;
use Nopadi\Http\Auth;
use Nopadi\Http\Request;
use Nopadi\Support\ACL;
use Nopadi\MVC\Controller;
use Nopadi\FS\ReadArray;

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

   public function getPermissions()
   { 
	$data = array('permissions'=>$this->permissions);

	 return view('@Painel/Views/settings/permission',$data);
   }

   public function create()
   {
	  return view('@Painel/Views/settings/permission-create');
   }
} 