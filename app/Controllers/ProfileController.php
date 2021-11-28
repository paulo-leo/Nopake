<?php 
namespace App\Controllers; 

use Nopadi\Http\Request;
use Nopadi\Http\Auth;
use App\Models\UserModel;
use App\Controllers\UserController;
use Nopadi\MVC\Controller;
use Nopadi\FS\UploadImage;


class ProfileController extends Controller
{
 
   /*Mostar o perfil do usuário*/
   public function index()
   {
     //Busca pelo usuário por meio do ID
	  $find = UserModel::model()->find(user_id());
	   
	  if($find){
		  
	   $langOptions = options($this->users()->langs(),$find->lang);
	   
       view('dashboard/users/profile',[
	       'page_title'=>text(':user.edit'),
	       'find'=>$find,
		   'langOptions'=>$langOptions]);
	   
	   }else view('404');
   }
   public function store()
   {
	       $request = new Request();
	   
		   $pass = $request->get('password');
		   $pass1 = $request->get('password-1');
		   $pass2 = $request->get('password-2');

		   if(Auth::checkPassword($pass,user_id())){
			  if($pass1 == $pass2 && strlen($pass1) > 5){
			  if($pass1 != $pass){
			    if(Auth::passwordUpdateManual($pass1,user_id())) 
					hello(':password_update_success','success');
				else hello(':password_update_error','danger');
			  }else hello(':equal_password','danger');
			  
		     }else hello(':passwords_do_not_match','danger');
		  }else hello(':invalid_password','danger'); 
   }
   /*Salva uma imagem de perfil*/
   public function upload(){
   //id do usuário
	$id = user_id();

	$remove = new Request();
	$remove = $remove->get('remove-image');
	
	if(!$remove){
    //Opções de criação da imagem
	$options = array(
	    'folder'=>'uploads/avatar/',
		'name'=>'userfile',
		'new_name'=>$id,
		'height'=>150,
		'width'=>150);

	 $file = new  UploadImage($options);
	 $save = $file->save();
	 $message = ':'.$file->getMessage();

	 if($save){
		
		if(user_image())
			if(user_image($save) != user_image()) user_image_remove();

		 Auth::setSession('image',$save);
		 Auth::imageUpdate($save,$id);

		 hello(text(':change_profile_picture_success').' '.text(':change_page_for_update'),'success');

	 }else{
		 hello($message,'danger');
	 }
	}else{
		if(user_image_remove()){
			 Auth::setSession('image',null);
		     Auth::imageUpdate(null,$id);
			 hello(text(':remove_profile_picture_success').' '.text(':change_page_for_update'),'success');
			
		} else hello(':remove_profile_picture_error','danger');
	}
   
   }

   /*Cria uma instancia da classe de usuários*/
   public function users(){
	   return new UserController;
   }
} 
