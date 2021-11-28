<?php 
namespace Modules\Painel\Controllers; 

use Nopadi\FS\Json;
use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Nopadi\Http\RouteCallback;


class SectorController extends Controller
{
   public function index()
   {
	  $list = $this->listSector();
	  return view('@Painel/Views/settings/sectors',['list'=>$list]);
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
			   
			   echo "<div class='np-quarter np-padding'>
                       <div class='np-card np-padding np-round-large np-center' style='height:90px'>
		               <a href='{$route}'>
			   <i class='material-icons np-border np-border-{$color} np-text-{$color} np-hover-{$color} np-padding np-round np-margin-bottom'>{$icon}</i></a><br>{$name}
		         </div></div>";
		   }
	   }
   }
 
   public function listSector($item=null)
   {
	   $json = new Json('config/access/sector.json');
	   $sector = $json->gets();
	   $return = "<ul class='np-ul'>";
	   
	    foreach($sector as $key=>$value){
			
		   extract($value);
		   $description = strlen($description) > 1 ? $description : 'Sem descrição';
           $class = is_null($item) ? 'np-hover-green np-pale-green np-border np-border-green' : 'np-hover-yellow np-pale-yellow np-border np-border-orange';
		   
		   $class = $status != 'active' ? 'np-hover-red np-pale-red np-border np-border-red' : $class;
		   
		  if($sub == $item)
		  {
		   $return .= 
		     "<li class='{$class} np-card np-margin-top np-animate-zoom np-padding np-white np-round-xxlarge'>
		         <p class='np-tooltip'>{$name}<span class='np-tag np-black np-text'>{$description}</span></p>
				 ".$this->listSector($key)."
		    </li>";
		  }
	   }
	  
	   return $return."</ul>";
   }
   
   
   public function listSectorSub($subItem)
   {
	   $json = new Json('config/access/sector.json');
	   $sector = $json->gets();
	   $return = "<ul class='np-ul'>";
	    foreach($sector as $key=>$value){
		   extract($value);
		   if($subItem == $sub){
		   $description = strlen($description) > 1 ? $description : 'Sem descrição';
           
		   $return .= 
		     "<li class='np-pale-green np-border np-margin np-padding np-white np-round-xxlarge'>
		         <span>{$name}</span>
		         <span>{$description}</span>
				 ".$this->listSectorSub($key)."
		    </li>";
		   }
	   }
	  
	   return $return."</ul>";
   }
   
   private function local($path){
	   $local = new URI;
	   return $local->local($path);
   }
} 





