<?php 
namespace App\Controllers; 

use Nopadi\Http\Auth;
use Nopadi\Http\Request;
use Nopadi\FS\Json;
use Nopadi\MVC\Controller;
use Nopadi\Base\CreateSchema;

class SettingController extends Controller
{
 
   /*Mostar o perfil do usuário*/
   public function index()
   {
	 if(Auth::check(['admin'])){
		  view('dashboard/settings',['page_title'=>'Configurações','status'=>$this->status()]);
	 }else view('dashboard/401');
    
   }
   
   public function status(){
	   $status = array(
	   'test'=>'Em teste',
	   'dev'=>'Em desenvolvimento',
	   'deploy'=>'Em produção'
	   );
	   return $status;
   }
   
   public function nwc(){
	  if(Auth::check(['admin','dev'])){
		  view('dashboard/settings-nwc',['page_title'=>'Nopadi Web CLI']);
	 }else view('dashboard/401');
   }
   
    public function store(){
		
		
		if(Auth::check(['dev','admin'])){
			    $code = new Request();
				$code = $code->get('code');
				
				if($code){
					 $json = new Json('config/app/web-cli.json');
					 $cmd = $json->get($code);
					 if($cmd){
						 $cmd = str_ireplace('/','\\',$cmd);
						 $this->callback($cmd);
					 }else{
						hello('O comando <b>"'.$code.'"</b> não está definido no arquivo "<b>web-cli.json</b>".','warning'); 
					 }
					 //callback($code)
				}else hello('Comando não pode ser vazio.','warning');
		  }
	}

 private function callback($callback,$params=null){
	           $cmd = null;
	 
	            $callback = explode('@', $callback);
				$controller = $callback[0];
				$method = $callback[1];
			    $params = is_null($params) ? array() : $params;
				
			    $rc = new \ReflectionClass($controller);

				if($rc->isInstantiable() && $rc->hasMethod($method))
				{
					$cmd .= call_user_func_array(array(new $controller, $method), array_values($params));
					
				} else {
                    hello('A aplicação <b>'.NP_NAME.'</b> informa que não é possível execultar o comando de callback, pois o método referenciado pelo comando não pode ser instanciado ou a classe não exite.','danger');				
				}
				
				
	 
 }
 
 public function creteSchema(){
	  
	  $create = new CreateSchema();
	  $create->executeAll();
	  
 }
 
  public function modifySchema(){
	  
	  $create = new CreateSchema();
	  $create->executeAll('modify');
	  
 }
 
  public function showSchema(){
	  
	  $create = new CreateSchema();
	  $create->showMysql();
	  
 }
} 
