<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\FS\Json;
use Nopadi\Http\Auth;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Modules\Painel\Controllers\RoleController;
use Modules\Painel\Models\UserModel;

class UserController extends Controller
{
	public static function mask($key){
		
		return 'teste';
		
	}
   /*Exibe todos os usuários por meio da paginação*/
   public function index()
   {  
	 if(Auth::check(['admin'])){
	  $filter = new Request();
	  
	  $status = $filter->get('status');
	  $role = $filter->get('role');
	  
	  $list = UserModel::model()
	      ->orderBy('id desc')
	      ->paginate(10,true); 
	   

     return view('@Painel/Views/users/index',[
	             'page_title'=>text(':users'),
	             'list'=>$list,
				 'rolesOptions'=>options($this->roles(),$role),
				 'statusOptions'=>options($this->status(),$status)
				 ]);	
	 }else return view('401');
	
    }
	
	/*Retonar os setores cadastrados*/
	public function sector()
	{
		$json = new Json('config/access/sector.json');
		$sector = $json->gets();
		
		$array = array();
		if(count($sector) >= 1){
		foreach($sector as $key=>$val)
		{
			$array[$key] = text($val['name']);
		}
		}else{
			$array[''] = text(':not_sector');
		}
		return $array;
	}
	
   /*Retonar o tipo ou função do usuário*/
   public function roles($name=null)
   {
		$roles = [
		'subscriber'=>':subscriber',
		'collaborator'=>':collaborator',
		'author'=>':author',
		'editor'=>':editor',
		'admin'=>':admin'];
		
		
		$role_person = new RoleController;
		$role_person = $role_person->getRoles();
		
		$roles = array_text($roles);
		
		$roles = array_merge($role_person, $roles);
		
		return  is_null($name) ? $roles : $roles[$name];

	}
	
   /*Retonar o estado do usuário*/
   public function status($name=null)
   {
		$status = [
		'pending'=>':pending',  /*Usuário foi criado, mas ainda não fez login*/
		'active'=>':active',    /*Usuário ativo, pois já realizou login*/
		'blocked'=>':blocked',  /*Usuário foi bloqueado por algum motivo*/
		'banned'=>':banned',    /*Usuário foi banido do sistema*/
		'disabled'=>':disabled' /*Usuário foi desativado*/
		];
		
		$status = array_text($status);
		
		return  is_null($name) ? $status : $status[$name];

	}
   
   /*Exibe o fomulário para editar o usuário*/
   public function edit()
   { 
	  if(Auth::check(['admin'])){
	  //Busca pelo usuário por meio do ID
	  $find = UserModel::model()->find($this->id());
	   
	  if($find){
		  
	   $roleOptions = $this->roles();
	   $statusOptions = $this->status();
	   $langOptions = $this->langs();
	   
       return view('@Painel/Views/users/edit',[
	       'page_title'=>text(':user.edit'),
	       'find'=>$find,
		   'statusOptions'=>$statusOptions,
		   'langOptions'=>$langOptions,
		   'sectorOptions'=>$this->sector(),
		   'roleOptions'=>$roleOptions]);
	   
	  }else return view('@Painel/Views/404');
	   }else return view('@Painel/Views/404');
   }
   
    public function passUser()
	{
		$find = UserModel::model()->find($this->id());
		if($find){
	   
       return view('@Painel/Views/users/pass',[
	       'page_title'=>'Alterar senha',
	       'find'=>$find]);
	   
	  }else return view('@Painel/Views/404');
	}
   
    public function show()
    { 
	  if(Auth::check(['admin'])){
	  //Busca pelo usuário por meio do ID
	  $find = UserModel::model()->find($this->id());
	   
	  if($find){
		  
	   $roleOptions = $this->roles();
	   $statusOptions = $this->status();
	   $langOptions = $this->langs();
	   
       return view('@Painel/Views/users/show',[
	       'page_title'=>text(':user.edit'),
	       'find'=>$find,
		   'statusOptions'=>$statusOptions,
		   'langOptions'=>$langOptions,
		   'sectorOptions'=>$this->sector(),
		   'roleOptions'=>$roleOptions]);
	   
	  }else return view('@Painel/Views/404');
	   }else return view('@Painel/Views/404');
   }
   
	/*Exibe o fomulário para criar um usuário*/
    public function create()
	{
	if(Auth::check(['admin'])){
		
	  $roleOptions = $this->roles();
	  $langOptions = $this->langs();

       return view('@Painel/Views/users/create',[
	   'page_title'=>text(':user.create'),
	   'langOptions'=>$langOptions,
	'roleOptions'=>$roleOptions]);
	}else return view('dashboard/401');
	   
   }
   
   /*Cria um usuário*/
    public function store()
	{
	   $request = new Request();
	   $request = $request->all();
	   
	   $request = Auth::create($request);
       $response = Auth::status();
	   
	   if($request){
		   
	      hello(alert(':user_create_success','success')); 
		   
	   }else{
		   
		   hello(alert(':'.$response,'danger'));  
	   }  
   }
   
   /*Atuliza um usuário*/
   public function update()
   {
	   $request = new Request();
	   
	   $id = $request->get('id');	   
	   $email = $request->getEmail('email');
	   $name = $request->getString('name','5:50');
	   $status = $request->get('status');
	   $role = $request->getString('role','3:50');
	   
	   $request->setMessages([
	   'name'=>'O nome do usuário deve conter entre 5 e 50 caracteres'
	   ]);
	   
	   $values = array(
	     'email'=>$email,
	     'name'=>$name,
	     'status'=>$status,
	     'role'=>$role
	   );
	   if($request->checkError())
	   {
		  $query = UserModel::model()->update($values,$id);
		  if($query)return alert(':user.update.success','success');
	      else return alert(':user.update.error','danger');
		  
	   }else
	   {
		  return alert($request->getMessages(true),'danger'); 
	   }
	  
   }
   
   /*Atuliza senha do usuário*/
   public function updatePassword()
   {
	   $request = new Request;
	   
	   $password = $request->get('password');
	   $id = $request->get('id');
	   $pass1 = trim($request->get('pass1'));
	   $pass2 = trim($request->get('pass2'));
	   
	   if($pass1 == $pass2 && strlen($pass1) >= 6){
		   
		  if(Auth::checkPassword($password, user_id())){
			  
			  if(Auth::passwordUpdateManual($pass1, $id)){
				  hello(alert('Senha atualizada com sucesso.','success')); 
			  }else{
				  hello(alert('Não foi possível atualizar a sua senha.','danger')); 
			  }
			  
		  }else{
			   hello(alert('Senha de administrador não confere.','danger'));
		  }
		   
	   }else{
		    hello(alert('Senhas não conferem ou não atendem a quantidade mínima de carcteres(6).','danger'));
	   }
   }
   
   /*Apagar um usuário*/
   public function destroy()
   {
	   $request = new Request();
	   
	   $id = $request->get('id'); hello('ok');

	   $query = (user_id() != $id) ? UserModel::model()->delete($id) : false;
	   
	   if($query) hello('ok');
	   else hello(':user.delete.error','danger'); 
   }

  /*Retonar o idioma do usuário*/
   public function langs($name=null)
   {
		$langs = [
		 'pt-br'=>'Portugês do Brasil',
		 'en'=>'Inglês'
		];
		
		return  is_null($name) ? $langs : $langs[$name];
	}
   
   /*busca uma filial*/
   public function search()
   {
     $request = new Request;
     $page = $request->get('page');
     $search = $request->get('search');

     $datas = UserModel::model();
	 $search = trim($search);

     if(strlen($search) >= 1)
     {
       $sql = "SELECT id, name, email FROM users WHERE email = '{$search}' OR name LIKE '%{$search}'";
     }else{
		$sql = "SELECT id, name, email FROM users";
	 }
     
     $datas = $datas->firstQuery($sql);
     $datas = $datas->paginate();

     $data = null;

     foreach ($datas->results as $values) 
     {
     	extract($values);
     	$data[] = array("id"=>$id, "text"=>$name.' | '.$email);
     }
      $data[] = array("paginate"=>true);
     
     return json($data);
   }
   
   
   public function records()
   {  
	    $list = UserModel::model()
	    ->orderBy('id desc')
	    ->paginate(10); 
	
	     return json($list);
   }
} 





