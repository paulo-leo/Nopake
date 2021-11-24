<?php 
/*
*Controlador nativo, responsável pelo gerenciamento de usuários. 
*/
namespace App\Controllers; 

use Nopadi\Http\Request;
use Nopadi\Http\Auth;
use App\Models\UserModel;
use Nopadi\MVC\Controller;


class UserController extends Controller
{
   
   /*Retonar o tipo ou função do usuário*/
   public function roles($name=null)
   {
		$roles = [
		'subscriber'=>':subscriber',
		'client'=>':client',
		'affiliated'=>':affiliated',
		'partner'=>':partner',
		'franchise'=>':franchise',
		'collaborator'=>':collaborator',
		'author'=>':author',
		'editor'=>':editor',
		'dev'=>':dev',
		'admin'=>':admin',
		'demo'=>':demo'];
		
		$roles = array_text($roles);
		
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
   
   /*Exibe todos os usuários por meio da paginação*/
   public function index()
   {
	   
	 if(Auth::check(['admin'])){
	  $filter = new Request();
	  
	  $status = $filter->get('status');
	  $role = $filter->get('role');
	  
	  $status = ($status && $status != '0') ? $status : false;
	  $role = ($role && $role != '0') ? $role : false;
	  
	  if($status && !$role){
		  $filter = array(
		   ['status',$status]
		   );
	  }elseif($role && !$status){
		  $filter = array(
		   ['role',$role],
		   );
	  }elseif($status && $role){
		  $filter = array(
		   ['role',$role],
		   ['status',$status]
		   );
	  }else{
		  
		 $list = UserModel::model()
	      ->orderBy('id desc')
	      ->paginate(); 
	  }
	  
	  if($status || $role){
		  $list = UserModel::model()
	      ->where($filter)
	      ->orderBy('id desc')
	      ->paginate(); 
	  }
	  

     view('dashboard/users/all',[
	             'page_title'=>text(':users'),
	             'list'=>$list,
				 'rolesOptions'=>options($this->roles(),$role),
				 'statusOptions'=>options($this->status(),$status)
				 ]);	
	 }else view('dashboard/401');
    }
   
   /*Exibe o fomulário para editar o usuário*/
   public function edit()
   {
	   
	  if(Auth::check(['admin'])){
	  //Busca pelo usuário por meio do ID
	  $find = UserModel::model()->find($this->id());
	   
	  if($find){
		  
	   $roleOptions = options($this->roles(),$find->role);
	   $statusOptions = options($this->status(),$find->status);
	   $langOptions = options($this->langs(),$find->lang);
	   
       view('dashboard/users/edit',[
	       'page_title'=>text(':user.edit'),
	       'find'=>$find,
		   'statusOptions'=>$statusOptions,
		   'langOptions'=>$langOptions,
		   'roleOptions'=>$roleOptions]);
	   
	  }else view('dashboard/404');
	   }else view('dashboard/401');
   }
   
	/*Exibe o fomulário para criar um usuário*/
    public function create()
	{
	if(Auth::check(['admin'])){
		
	  $roleOptions = options($this->roles());
	  $langOptions = options($this->langs());

       view('dashboard/users/add',[
	   'page_title'=>text(':user.create'),
	   'langOptions'=>$langOptions,
	'roleOptions'=>$roleOptions]);
	}else view('dashboard/401');
	   
   }
   
   /*Cria um usuário*/
    public function store()
	{
		
	   $request = new Request();
	   $request = $request->all();
	   
	   $request = Auth::create($request);
       $response = Auth::status();
	   
	   if($request){
		   
	      hello(':user_create_success','success'); 
		   
	   }else{
		   
		   hello(':'.$response,'danger');  
	   }  
   }
   
   /*Atuliza um usuário*/
   public function update()
   {
	   $request = new Request();
	   
	   $id = $request->get('id');
	   $values = $request->all('id');
	   
	   $query = UserModel::model()->update($values,$id);
	   
	   if($query) hello(':user.update.success','success');
	   else hello(':user.update.error','danger');
	   
   }
   
   /*Apagar um usuário*/
   public function destroy()
   {
	  
	   $request = new Request();
	   
	   $id = $request->get('id');
	   
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
} 
