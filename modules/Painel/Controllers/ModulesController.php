<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\FS\Json;
use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Nopadi\Http\RouteCallback;
use ZipArchive;


class ModulesController extends Controller
{
   public function index()
   {
	  $request = new Request;
	  
	  $all = $request->get('all',null);
	  
	  $list = $this->listModules($all);
	  $qtd = count($list);
	  
	  switch($all){
		  case 'active' :  $title = "Módulos habilitados ({$qtd})"; break;
		  case 'disabled' :  $title = "Módulos desabilitados ({$qtd})"; break;
		  default :  $title = "Todos os módulos ({$qtd})";
	  }
	  
	  return view('@Painel/Views/settings/modules',['list'=>$list,'page_title_type'=>$title]);
   }
   
   public function import()
   {
	  
	  return view('@Painel/Views/settings/import-module');
   }
   
   public function importModule()
   {  
	   $request = new Request;
	   $file = $request->getFile('userfile','name');
	   
	      if($file){
		   $arquivo = $request->getFile('userfile','tmp_name');
           $destino = '../modules';
		   
    
           $zip = new ZipArchive;
           $zip->open($arquivo);
           if($zip->extractTo($destino) == TRUE)
           {
             return "<p class='alert alert-primary' role='alert'>Módulo importado e descompactado com sucesso.</p>";
           }
          else
          {
            return "<p class='alert alert-danger' role='alert'>O Arquivo não pode ser descompactado.</p>";
          }
          $zip->close();
	      }
   }
   
   public function apps()
   {
	   $json = new Json('config/app/modules.json');
	   $modules = $json->gets();
	   $return = null;
	   foreach($modules as $key=>$value){
		   extract($value);
		   if($status == 'active' && strlen($route) >= 2){
			   
			   $icon = strlen($icon) > 2 ? $icon : 'widgets';
			   $color = strlen($color) > 2 ? $color : 'blue';
			   $route = url($route);
			   
			   echo "<div class='np-quarter np-padding np-center'>
                       <div class='np-padding' style='height:90px;width:90px'>
		               <a href='{$route}'>
			   <i class='material-icons np-animate-zoom np-border np-border-{$color} np-text-{$color} np-hover-{$color} np-padding np-round np-margin-bottom np-card'>{$icon}</i></a><br><div class='np-tooltip'>{$name}<span style='font-size:12px;position:absolute;left:0;z-index:2;bottom:30px'
class='np-text np-tag'>{$description}</span></div>
		         </div></div>";
		   }
	   }
   }
   public function menu(){
	   
	   $json = new Json('config/app/modules.json');
	   $modules = $json->gets();
	   
   }
   public function listModules($all=null)
   {
	   $json = new Json('config/app/modules.json');
	   $modules = $json->gets();
	   $return = null;
	   
	   $mds = array();
	   foreach($modules as $key=>$value)
	   {
		 if($all == 'active' && $value['status'] == 'active')
		 {
            $mds[$key] = $value;
		 } 
		   
		 if($all == 'disabled' && $value['status'] == 'disabled')
		 {
            $mds[$key] = $value;
		 }
		 
		 if($all == null)
		 {
            $mds[$key] = $value;
		 }
	   }
	   
	   /*
	   foreach($modules as $key=>$value){
		   extract($value);
		   $id = 'module-'.$key;
		   $description = strlen($description) > 1 ? $description : 'Sem descrição';
		   $btn_checked = $status == 'disabled' ? 0 : 1;
		   
          $btn = form_switch('np-btn-mod-status',$btn_checked,$key);
		
				 
		
		  $return .= '<div class="col-3 p-1">
                         <div class="card shadow-sm">
		                  <h4 class="card-title">'.$name.'</h4>
                           <p>'.trim(substr($description,0,80)).'...</p>
                          
                          <div class="card-action">'.$btn.'</div>
						  
						  </div>
                      </div>';
	   }
	   */
	   return $mds;
   }
   
   public function listValues($value)
   {
	   $data = null;
	   if(is_array($value)){
		 if(count($value) > 0){ 
            $data .= "<ul class='np-ul'>";		 
		   foreach($value as $val){
			   $data .= "<li>".$val."</li>";
		   }
		   $data .= "</ul>";	
		 return $data;
		 
		 }else{
			 return 'Não especificado';
		 }
	   }else{
		   return strlen($data) >= 1 ? $data : 'Não especificado';
	   }
   }
   
   public function register($all=false)
   {
	  $json = new Json('config/app/modules.json'); 
	  $path = $this->local('modules');
	  $total = 0;
	  $d = dir($path);
          while (false !== ($entry = $d->read())) 
		  {   
		      if($entry != '.' && $entry != '..'){
				 
				  if(!$json->exists_key($entry) || $all){
					  
					  $pathModule = $this->local('modules/'.$entry.'/config.json');
					  
					  $pathModule = new Json($pathModule);
					  
					  if($pathModule->has()){
						  
					     if($pathModule->exists_key('name') && $pathModule->exists_key('version')){
							 if(strlen(trim($pathModule->get('name'))) > 1 && strlen(trim($pathModule->get('version'))) > 0){
								
								$name = $pathModule->get('name');
								$version = $pathModule->get('version');
								$author = $pathModule->exists_key('author') ? $pathModule->get('author') : "";
								$url = $pathModule->exists_key('url') ? $pathModule->get('url') : "";
								$license = $pathModule->exists_key('license') ? $pathModule->get('license') : "";
								$description = $pathModule->exists_key('description') ? $pathModule->get('description') : "";
								$require = $pathModule->exists_key('require') ? $pathModule->get('require') : "";
								$route = $pathModule->exists_key('route') ? $pathModule->get('route') : "";
								$color = $pathModule->exists_key('color') ? $pathModule->get('color') : "";
								$icon = $pathModule->exists_key('icon') ? $pathModule->get('icon') : "";
								$parent = $pathModule->exists_key('parent') ? $pathModule->get('parent') : "";
								
								$status = $this->parentActive($entry) ? 'active' : 'disabled';
								
								$json->set($entry,array(
					             'name'=>$name,
					             'description'=>$description,
					             'author'=>$author,
					             'status'=>$status,
					             'version'=>$version,
								 'license'=>$license,
								 'require'=>$require,
					             'url'=>$url,
								 'route'=>$route,
								 'parent'=>$parent,
								 'color'=>$color,
								 'icon'=>$icon
								 ));
								 $total++;
							 }
						 }  
					  }
				  }
			  }
          }
         $json->save();		 
         $d->close();
		 return $total;
   }
   
   public function update()
   {
	   $key = new Request;
	   $key = $key->get('key');
	   $json = new Json('config/app/modules.json');
	   if($json->exists_key($key))
	   {
		 $status = $json->get($key,'status');
		 $parent = $json->get($key,'parent');
		 $name = $json->get($key,'name');
		 
		 if($status == 'disabled')
		 { 
			 if($this->parentActive($parent)){
				 
				$call = new RouteCallback;
				$callback = $key.'@active';
				$namespace = 'Modules\\'.$key.'\\';
			    $params = array();
			    $call = $call->execute($callback,$namespace);
				 
	            $json->set($key,'status','active');
				hello(alert("Módulo \"{$name}\" ativado com sucesso.","success"));  
				
			 }else{
				 
				 $parent = explode('|',$parent);
	             $parent = isset($parent[1]) ? $parent[1] : $parent[0];
				 hello(alert("Não foi possível ativar o módulo \"{$name}\", pois este módulo depende do módulo pai \"{$parent}\", que não está ativo ou instalado no sistema.","danger"));  
			 }
		 }
		 else
		 {
			 $namespace = 'Modules\\'.$key.'\\';
			 $params = array();
			 $callback = $key.'@disabled';
			 $deps = $this->activesParentDep($key);
			 
			 if($deps){
				 
				 hello(alert("Não foi possível desativar o módulo \"{$name}\", pois os seguintes módulos dependem deste módulo: {$deps}\n\n Será necessário desativar os módulos filhos/dependentes primeiro para depois desativar o módulo {$name}.","danger"));
				 
			 }else{
				 
				 $call = new RouteCallback;
				 $call = $call->execute($callback, $namespace);
				 $json->set($key,'status','disabled');
				 hello(alert("Módulo \"{$name}\" desativado com sucesso.","success"));
			 }
		 }
		 $json->save(); 
	   }
   }

    private function parentActive($value)
	{ 
	   $value = explode('|',$value);
	   $value = $value[0];
	   
	  if(strlen($value) >= 2){ 
	      return in_array($value, $this->actives());
	  }else{
		 return true; 
	  }
	}
	
    private function actives()
	{
	   $json = new Json('config/app/modules.json');
	   $modules = $json->gets();
	   $array = array();
	   foreach($modules as $key=>$value)
	   {
		   if($value['status'] == 'active') 
		                array_push($array, $key);
	   }
	   return $array;
   }
   /*verifica se há algum módulo ativo e dependente*/
   private function activesParentDep($keyMod)
	{
	   $json = new Json('config/app/modules.json');
	   $modules = $json->gets();
	   $array = array();
	   foreach($modules as $key=>$value)
	   {
		   $parent = explode('|',$value['parent']);
		   $parent = $parent[0];
		   $name = $value['name'];
		   if($value['status'] == 'active' && $parent == $keyMod){
			    array_push($array, $name);
		   }            
	   }
	   
	   if(count($array) >= 1){
		   $mods = "";
		   $num = 1;
		   foreach($array as $v){
			   $mods .= "{$v},";
		   }
		   return $mods .= "";
	   }else{
		   return false;
	   }
   }
   
   /*Atualizar módulos*/
   public function updateJson()
   {
	  $request = new Request;
	  $type = $request->get('type');
	  if($type == 'all'){
		 $total = $this->register(true); 
		 hello(alert("Todos os módulos foram atualizados no total de {$total} módulo(s) mapeado(s).","success"));
	  }
	  if($type == 'new'){
		  $total = $this->register();
          $msg = $total > 0 ? 'Módulos registrados com sucesso.<br> Total de: <b>'.$total.'</b>' : 'Nenhum novo módulo foi registrado.'; 	  
		  hello(alert($msg,"success"));
	  }
   }
   
   private function local($path){
	   $local = new URI;
	   return $local->local($path);
   }
} 





