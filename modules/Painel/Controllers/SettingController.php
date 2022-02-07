<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\FS\Json;
use Nopadi\FS\ReadArray;
use Nopadi\Http\Auth;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Modules\Painel\Controllers\UserController;


class SettingController extends Controller
{
 
   /*Mostar o perfil do usuário*/
   public function index()
   {
	  if(Auth::check(['admin'])){
		  return view('@Painel/Views/settings/index',
		  ['page_title'=>'Configurações',
		  'status'=>$this->status()]);
	 }else return view('@Painel/Views/401');
   }

   public function rolesDefault($name=null)
   {
	  $roles = new UserController();
	  $roles = $roles->roles($name);
	  unset($roles['admin']);
	  unset($roles['dev']);
	   unset($roles['editor']);
      return $roles;  
   }
   
   public function lang($name=null)
   {
	  $lang = 
	    [
		'pt-br'=>'Português do Brasil',
		'en'=>'Inglês'
		];
		$lang = array_text($lang);
		return  is_null($name) ? $lang : $lang[$lang];
   }
   
   public function configEnvironment()
   {
	  if(Auth::check(['admin'])){
		  return view('@Painel/Views/settings/environment',
		  ['page_title'=>'Configurações',
		  'status'=>$this->status(),'lang'=>$this->lang(),'mode'=>$this->mode(),'np_timezone'=>$this->	np_timezone(),'new_member_default'=>$this->rolesDefault()]);
	 }else return view('@Painel/Views/401');
   }
   
   public function saveEnvironment(){
	    $request = new Request;
	    $json = new Json('config/app/app.json');
	    
	    $name = $request->get('name');
		$author = $request->get('author');
		$description = $request->get('description');
		$lang = $request->get('lang');
		$status = $request->get('status');
		$timezone = $request->get('timezone');
		$mode = $request->get('mode');
		$logs = $request->get('logs','off');
		$strong_password = $request->get('strong_password','off');
		$charset = $request->get('charset');
		$max_execution = $request->get('max_execution');
		$new_member_default = $request->get('new_member_default');
		$new_members = $request->get('new_members','off');
		$accept_terms = $request->get('accept_terms','off');
		$active_api = $request->get('active_api','off');

		
		$check = null;
		
		if(strlen($name) < 2){
			
			$check .= '*O nome da aplicação não pode ser vazio!';
		}
		
		if(strlen($description) < 10){
			
			$check .= '*A descrição da aplicação não pode ser vazio ou menor que 10 caracteres!';
		}
		
		if(strlen($author) < 2){
			
			$check .= '*O nome do autor não pode ser vazio!';
		}

		 if(!$check){
			 
			 $json->set('name',$name);
		     $json->set('description',$description);
		     $json->set('author',$author);
			 $json->set('lang',$lang);
			 $json->set('status',$status);
			 $json->set('timezone',$timezone);
			 $json->set('mode',$mode);
			 $json->set('logs',$logs);
			 $json->set('charset',$charset);
			 $json->set('max_execution',$max_execution);
			 $json->set('new_member_default',$new_member_default);
			 $json->set('new_members',$new_members);
			 $json->set('accept_terms',$accept_terms);
			 $json->set('strong_password',$strong_password);
			 $json->set('active_api',$active_api);
			 
			 
			 if($json->save()){
				 
				 hello(alert('Configurações de ambiente salvas com sucesso.','success'));

			 }else{
				  hello(alert('Erro ao tentar salvar configurações. Tente novamente mais tarde.','danger'));
			 }
		 }else{
			 hello(alert($check,'danger'));
		 }
	
   }
   
   public function saveSMTP()
   {
	    $request = new Request;
	    $array = new ReadArray('config/smtp/smtp.php');
		
		$auth = $request->getBool('auth');
		$port = $request->getInt('port');
	    $pass = $request->get('pass');
		$host = $request->get('host');
		$email = $request->get('email');
		$user = $request->get('user');
		$name = $request->get('name');
		
	    $array->set('auth',$auth);
		$array->set('port',$port);
		$array->set('pass',$pass);
		$array->set('host',$host);
		$array->set('user',$user);
		$array->set('email',$email);
		$array->set('name',$name);
		
		if($array->save()){
			hello(alert('Configurações de saída de e-mail salvas com sucesso.','success'));
		}else{
			hello(alert('Erro ao salvar configurações.','danger'));
		}
   }
   
   public function saveTheme()
   {
	     $request = new Request;
	     $json = new Json('config/app/theme.json');
	   
	     $theme = $request->get('theme');
         $json->set('theme',$theme);
		 $json->save();
		 hello(alert('Tema salvo com sucesso','success')); 
   }
   
   public function saveKeyAPI()
   { 
	     $request = new Request;
	     $json = new Json('config/app/app.json');
	   
	     $value = $request->get('key_api');
		 $value = md5(user_id().date('y-m-s-i-s-d'));
         $json->set('key_api',$value);
		 $json->save();
		 hello($value);
   }
   
   /*Retorna o status*/
   public function status(){
	   $status = array(
	   'test'=>'Em teste',
	   'dev'=>'Em desenvolvimento',
	   'deploy'=>'Em produção',
	   'maintenance'=>'Manutenção'
	   );
	   return $status;
   }
   /*Retorna os modos*/
   public function mode(){
	   $mode = array(
	   'dev'=>'Desenvolvimento',
	   'test'=>'Teste',
	   'approved'=>'Homologação',
	   'deploy'=>'Produção',
	   'maintenance'=>'Manutenção'
	   );
	   return $mode;
   }
   
    /*Retorna os modos*/
   public function np_timezone(){
	   $mode = array(
	   'America/Sao_Paulo'=>'São Paulo',
	   'America/Porto_Velho'=>'Porto Velho',
	   'America/Maceio'=>'Maceio',
	   'America/Campo_Grande'=>'Campo Grande',
	   'America/Boa_Vista'=>'Boa Vista',
	   'America/Manaus'=>'Manaus',
	   'America/Bahia'=>'Bahia',
	   'America/Belem'=>'Belém',
	   'America/Fortaleza'=>'Fortaleza',
	   'America/Cuiaba'=>'Cuiaba',
	   'America/Rio_Branco'=>'Rio Branco'
	   );
	   return $mode;
   }
} 
