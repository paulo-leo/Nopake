<?php
/*
*
*Author: Paulo Leonardo da Silva Cassimiro
*
*/

namespace Nopadi\Support;

use Nopadi\Http\Auth;
use Nopadi\FS\ReadArray;

class ACL
{
	private $fileDir = 'config/access/access.php';
	private $instance;
	private $permissions = array();
	private $msg_error;

	public function __construct()
	{
	   $this->instance = new ReadArray($this->fileDir);
	   $this->permissions = $this->instance->gets();
	   $this->msg_error = 'Você não tem permissão para acessar este recurso! Entre em contato com o administrador do sistema.';
	}	
	
	public function check($permissions=null,$role=null)
	{
	   	$role = (is_null($role) || !is_string($role)) ? Auth::user()->role : $role;
		
		$access = false;
		
		   if($role)
		   {
			if($role == 'admin')
			{
				$access = true;
			}else
			{
			 if(array_key_exists($role,$this->permissions))
			 {
			    $access = in_array($permissions,$this->permissions[$role]);
			 }
		    }
		   }
		
		return $access;
	}
	
	public function exit($permission,$msg=null)
	{
		
		$msg = is_null($msg) ? $this->msg_error : $msg;
		if(!$this->check($permission)){
			
			$msg = alert($msg,'danger');
			exit($msg);
		}
	}
}
