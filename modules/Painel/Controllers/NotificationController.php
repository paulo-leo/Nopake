<?php 
/*
Autor: Paulo Leonardo da 
Descrição: classe responsável pelo gerenciamento de notificações do sistema
*/
namespace Modules\Painel\Controllers; 

use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Modules\Painel\Models\NotificationModel as Notification;

class NotificationController extends Controller
{
	
   public function index()
   {
	   $id = user_id();
	   if($id){ 
       $notifications = Notification::model()->all("user_id = ".$id);
	  
	   if($notifications){ 
		foreach($notifications as $notification)
		{
			extract($notification);
			$message = "<a id='{$id}' class='np-link np_notification_delete' href='{$link}'>{$message}<br><b class='np-small'>{$created_in}</b></a>";
			hello("<li class='np_notification_item-{$id} np-hover-light-gray np-animate-zoom'>{$message}</li>");
		} 
	   }else{
		   hello("<li><h4 class='np-center'><i class='material-icons np-animate-zoom np-xxlarge'>notifications_off</i><br>No momento não há notificações.</h4></li>");
	   }
	 }
   }
   
   
   public function total()
   {
	   $id = user_id();
	   if($id){ 
       $total = Notification::model()->count("user_id = ".$id);
	     echo $total;
	   }else{ echo 0;}
   }
   
    public function store()
	{
	   $request = new Request();
	   $message = $request->get('message');
	   $link = $request->get('link');
	   
	   if($request){
		   
	      hello(alert(':user_create_success','success')); 
		   
	   }
   }
   public function create(){
	   
	   $_GET['teste'] = 'true';
	   
	   $request = new Request();
	   var_dump($request->getBool('teste'));
   }
   
   /*Apagar uma notificação*/
   public function destroy()
   {
       if(user_id()){
		   $request = new Request();
	       $id = $request->get('id'); 
	       Notification::model()->delete($id);
	   }  
   }
} 





