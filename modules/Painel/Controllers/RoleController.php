<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\FS\Json;
use Nopadi\Http\Auth;
use Nopadi\Http\Request;
use Nopadi\Support\ACL;
use Nopadi\MVC\Controller;
use Nopadi\FS\ReadArray;

class RoleController extends Controller
{

   private $fileDir = 'config/access/roles.json';
   private $fileDirPemission = 'config/access/permissions.json';
   private $instance;
   private $roles = array();
   private $msg_error;
   
   public function __construct()
   {
	   $this->instance = new Json($this->fileDir);
	   $this->roles = $this->instance->gets();  
   }


   public function getRoles()
   {
	  $roles = array();
	  foreach($this->roles as $key=>$val){
		  $roles[$key] = $val['name'];
	  }
	  
	  unset($roles['admin']);
	  return $roles;
   }	
   
   public function index()
   {
	   
	   $data = array(
	    'roles'=>$this->roles
	   );
	   
       return view('@Painel/Views/settings/roles',$data);
   }
   
   public function roleEditPermissions()
   {
	  $f = new Json($this->fileDirPemission);
	  $f = $f->gets(); 
	  $acl = new ACL;
	  $request = new Request;
	  $role = $request->get('role');
	  $name = $request->get('name');
	  
	  $token = csrf_token();
	  $form = null;
	  $form .= "<input type='hidden' name='_token' value='{$token}'>
	  <input type='hidden' name='np_role' value='{$role}'>";
	  
	   foreach($f as $key=>$val)
	   {
		   
		   $checked = $acl->check($key,$role) ? 'checked="checked"' : " ";
		   
		    $form .= "
			<div class='form-check form-switch'>
             <label>
              <input type='checkbox' name='perm[]' value='{$key}' class='form-check-input' role='switch' id='{$key}' {$checked}/>
             <label class='form-check-label' for='{$key}'>{$val}</label>
            </div>";
			
	   }
		
	  $data = array(
	    'form'=>$form,
		'name'=>$name
	   );
	   
      return view('@Painel/Views/settings/roles-edit-permissions',$data);

   }
  public function roleUpdatePermissions()
  {
	  
	  $request = new Request;
	  $role = $request->get('np_role');
	  $permissions = options_post('perm');
	  
	  $access = 'config/access/access.php';
	  $access = new ReadArray($access);

	  if($permissions){
		   $access->set($role,$permissions);
		   $alert = alert('Permissões atualizadas com sucesso','success');
	  }else{
		  
		   $access->del($role);
		   $alert = alert('Todas as permissões foram revogadas.','info');
	  }
	 
	  if($access->save(true)){
		  hello($alert);
	  }else{
		   hello(alert('Erro ao tentar atualizar permissões.','danger'));
	  }
  }
  
  public function roleFormCreate()
  {
	  return view('@Painel/Views/settings/role-create');
  }
  
  public function roleStore()
  {
	$request = new Request;
	$roles = new Json($this->fileDir);  

	$name = $request->get('name');
	$description = $request->get('description');
	$key = str_url($name);
	
	if(strlen($name) >= 2){
		
	if(!$roles->exists_key($key))
	{
		$roles->set($key,array(
		'name'=>$name,
		'description'=>$description,
		'sub'=>'',
		'status'=>'active'
		));
		
		if($roles->save()){
			 return alert("Função \"<b>{$name}</b>\" foi adicionada com sucesso.","success");
		}else{
			return alert('Arquivo não salvo','danger');
		}
		
	}else{
		
		return alert('Essa função já existe!','danger');
		
	}
	}else{
		return alert('Nome da função é muito curto.','danger');
	}
  }
  
  public function rolePermissionsStore()
  {
	$request = new Request;
	$roles = new Json($this->fileDirPemission);  

	$key = $request->get('key');
	$description = $request->get('description');
	$key = str_url($key);
	
	if(strlen($key) >= 2){
		
	if(!$roles->exists_key($key))
	{
		$roles->set($key,$description);
		
		if($roles->save()){
			 return alert("Permissão para <b>{$description}</b> registrada com sucesso. ","success");
		}else{
			return alert('Arquivo não salvo','danger');
		}
		
	}else{
		
		return alert('Essa permissão já existe!','danger');
		
	}
	}else{
		return alert('Nome da permissão é muito curto.','danger');
	}
  }
} 





