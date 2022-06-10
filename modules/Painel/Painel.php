<?php 
namespace Modules\Painel; 

use Nopadi\MVC\Module;
use Nopadi\Http\Request;
use Nopadi\Http\Route;
use Nopadi\Http\Param;
use Dompdf\Dompdf;

class Painel extends Module
{
	public function main(){
		$records = array(
		  'get:record'=>'executeRecord'
		);
		
        $login = array(
           'get:login'=>'showLoginForm',
		   'post:login'=>'login',
		   'get:logout'=>'logout'
       );
	   
	   $login_jwt = array(
	     'post:api/jwt/register'=>'Register',
	     'post:api/jwt/login'=>'Login'
	   );
	   
	   $register = array(
	      'get:register'=>'registerNow',
		  'get:register/{token}'=>'registerNowCheck',
		  'post:register/code'=>'code',
		  'post:register/code/check'=>'codeCheck',
		  'post:register'=>'register'
	   );

       $password = array(
		   'get:password/reset'=>'showLinkRequestForm',
		   'post:password/email'=>'sendResetLinkEmail',
           'get:password/reset/{token}'=>'showResetForm',
		   'post:password/reset'=>'reset'
       );
	   
	   $profile = array(
		   'dashboard/profile'=>'index',
		   'dashboard/profile/edit'=>'edit',
		   'dashboard/profile/password'=>'editPassword',
		   'dashboard/profile/image'=>'editImage',
		   'post:profile/upload'=>'updateAvatar',
		   'post:profile/update'=>'profileUpdate',
		   'post:profile/avatar'=>'updateAvatar',
		   'get:profile/avatar'=>'getAvatar',
		   'post:profile/password'=>'updatePassword',
		   'delete:profile/remove'=>'removeImage'
       );
	   
	   /*
	    Módulos
	   */
	   
	   $modules = array(
		   'get:dashboard/settings/modules'=>'index',
		   'get:dashboard/settings/modules/import'=>'import',
		   'post:dashboard/settings/modules/import'=>'importModule',
		   'get:dashboard/settings/modules/apps'=>'apps',
		   'post:dashboard/settings/modules'=>'update',
		   'post:dashboard/settings/modules/update'=>'updateJson'
       );
	   
	   
	   /*
	    Funções e permissões
	   */
	   
	   $roles = array(
	       'get:dashboard/settings/roles/create'=>'roleFormCreate',
		   'post:dashboard/settings/roles/create'=>'roleStore',
		   'post:dashboard/settings/permissions/create'=>'rolePermissionsStore',
		   'get:dashboard/settings/roles'=>'index',
		   'get:dashboard/settings/roles/edit/permissions'=>'roleEditPermissions',
		   'post:dashboard/settings/roles/update/permissions'=>'roleUpdatePermissions',
		   'post:dashboard/settings/roles/delete'=>'removeRole'
       );
	   
	   $access = array(
		   'get:settings/sectors'=>'index'
       );

       $permissions = array(
		'dashboard/settings/permissions/create'=>'create',
		'dashboard/settings/permissions'=>'getPermissions',
		'post:dashboard/settings/permissions/remove'=>'remove'
	   );


	   Route::controllers($permissions,'@Painel/Controllers/PermissionController');
	   
	   Route::controllers($records,'@Painel/Controllers/RecordController');
	   
	   Route::controllers($roles,'@Painel/Controllers/RoleController');
	   
	   Route::get('dashboard/users/password/{id}','@Painel/Controllers/UserController@passUser');
	   
	   Route::resources('dashboard/users','@Painel/Controllers/UserController');
	   
	   $users  = array(
	   'post:dashboard/users/update/password'=>'updatePassword'
	   );
	   
	   Route::controllers($users,'@Painel/Controllers/UserController');
	   
	   
	   Route::controllers($access,'@Painel/Controllers/SectorController');
	   
       Route::controllers($modules,'@Painel/Controllers/ModulesController');
   
   
       Route::controllers($login,'@Painel/Controllers/LoginController');
	   Route::controllers($register,'@Painel/Controllers/RegisterController');
	   Route::controllers($password,'@Painel/Controllers/ForgotPasswordController');
	   
	   Route::controllers($login,'@Painel/Controllers/LoginController');
	   
	   
	   Route::post('api/jwt/password/update','@Painel/Controllers/LoginJWTController@updatePasswordJWT',['middleware'=>['AJWT']]);
	   Route::controllers($login_jwt,'@Painel/Controllers/LoginJWTController');
	   
	   Route::controllers($profile,'@Painel/Controllers/ProfileController');
	   
	   Route::resources('dashboard/settings','@Painel/Controllers/SettingController');
	   
	   Route::resources('dashboard/notifications','@Painel/Controllers/NotificationController');
	   
	   Route::get('dashboard',function(){
		   return view('@Painel/Views/dashboard');
	   });
	   Route::get('dashboard/applications',function(){
		   return view('@Painel/Views/applications');
	   });
	   
	   Route::get('settings',function(){
		   return view('@Painel/Views/settings/index');
	   });
	   
	   Route::get('dashboard/settings/theme',function(){
		   return view('@Painel/Views/settings/theme');
	   });
	   
	   
	   Route::post('api/np/xls/table',function(){
		   
	        $request = new Request;
		    $layout = $request->get('css');
		    if(!$layout)
		    {
			     $layout = "<style>
					                table,th,td{font-size:12px;font-family:arial;}
					                td,th{font-size:12px}
					          </style>";
					
			}else{ $layout = "<style>{$layout}</style>"; }
				
				
			$html = $request->get('content');
			$html = html_entity_decode($html);
				
		    $html = $layout.$html;
			
			header("Content-type: application/vnd.ms-excel");
            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=table.xls");	  
			
			hello(utf8_decode($html));
	   });
	   

	     Route::post('api/np/print/table',function(){

		        $request = new Request;
				$layout = $request->get('css');
				if(!$layout)
				{
					$layout = "<style>
					                table,th,td{font-size:12px;font-family:arial;}
					                td,th{font-size:12px}
					          </style>";
					
				}else{ 
				   $layout = "<style>{$layout}</style>";
				}
				
				
			    $html = $request->get('content');
			    $html = html_entity_decode($html);
				
				$html = $layout.$html;
				
			   $dompdf = new Dompdf(["enable_remote"=>true]);
			   $file_name ='table.pdf';
			   $dompdf->loadHtml($html);
	           //$dompdf->setPaper('A4', 'landscape');
			   $dompdf->set_paper("A4", "portrail");
	           $dompdf->render();
	           $dompdf->stream($file_name,["Attachment"=>false]);
	   });
	   
	   
	   
	   /*Config*/
	   Route::get('dashboard/settings/environment','@Painel/Controllers/SettingController@configEnvironment');
	   Route::post('dashboard/settings/theme','@Painel/Controllers/SettingController@saveTheme');
	   Route::post('dashboard/settings/environment','@Painel/Controllers/SettingController@saveEnvironment');
	   Route::post('dashboard/settings/smtp','@Painel/Controllers/SettingController@saveSMTP');
	   Route::post('dashboard/settings/key-api','@Painel/Controllers/SettingController@saveKeyAPI');

	   Route::get('dashboard/settings/db',function(){
		   return view('@Painel/Views/settings/db');
	   });

	   Route::get('dashboard/settings/smtp',function(){
         return view('@Painel/Views/settings/smtp');
	   });
	   Route::get('dashboard/settings/key-api',function(){
         return view('@Painel/Views/settings/key-api');
	   });
	}
	
	public function disabled(){
		
	}
} 