<?php
/*
*Arquivo de funções do Nopadi
*Author: Paulo Leonardo Da Silva Cassimiro
*/

use Nopadi\MVC\View;
use Nopadi\Http\Route;
use Nopadi\Http\URI;
use Nopadi\Http\Param;
use Nopadi\Http\Auth;
use Nopadi\FS\Json;
use Nopadi\FS\ReadArray;
use Nopadi\Http\Request;
use Nopadi\Support\Translation;
use Nopadi\Base\DB;

$GLOBALS['np_instance_of_view'] = new View;
$GLOBALS['np_instance_of_uri'] = new URI;
$GLOBALS['np_instance_of_json'] = new Json('config/app/hello.json');
$GLOBALS['np_instance_of_permissions'] = new Json('config/access/permissions.json');
$GLOBALS['np_instance_of_json_mods'] = new Json('config/app/modules.json');
$GLOBALS['np_instance_of_request'] = new Request;
$GLOBALS['np_instance_of_translater'] = new Translation;



function gets($name=null,$value=null)
{
    $values= null;
	if(!is_null($name))
	{
		$values = isset($_GET[$name]) ? $_GET[$name] : $value;
	}else{
		$values = $_GET;
	}
	return $values;
}

if(!function_exists('getallheaders'))
{
       function getallheaders()
       {
          $headers = [];
          foreach ($_SERVER as $name => $value)
          {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
         }
         return $headers;
      }
}

/*Encontra um determinado resultado na string*/
function contains($str,$search,$i=false)
{
	$i = $i ? null : 'i';
	return (preg_match("/{$search}/{$i}", $str)) ? true : false;
}
/*Define permissões de usuário para um aplicativo*/
function permission($permissions)
{
	$permissions = !is_array($permissions) ? array($permissions) : $permissions;
	$GLOBALS['np_permissions'] = $permissions;
}

/*Dá acesso ao usuário caso ele tenha permissões*/
function access($permissions=null)
{
	$check = false;
	$role = user()->role;
	$check = ($role == 'admin') ? true : false;
	
	if($role && $check == false)
	{    
      $file = 'config/access/access.php';
	  $access = new ReadArray($file);
	  $access = $access->get($role,false);
	  
	  
	   if($access)
	   {
		 if(is_null($permissions) && isset($GLOBALS['np_permissions']))
	     {
		   $permissions = $GLOBALS['np_permissions'];
	     }
		 
		$permissions = is_string($permissions) ? array($permissions) : $permissions;
		$permissions = is_null($permissions) ? array() : $permissions;
		
		$qtd = 0;
		foreach($permissions as $permission)
		{
			if(in_array($permission,$access))
			{
				$qtd++;
			}
		 }
		 
		  $check = $qtd > 0 ? true : false;
	   }
	}
	return $check;
}

/*
Cria ou atualiza permissões de função de usuários
*/
function create_permission($permissions,$update=false)
{
	$instance = $GLOBALS['np_instance_of_permissions'];
	$total = 0;
	foreach($permissions as $key=>$val)
	{
		$key = strtolower($key);
		if($update)
		{
			$instance->del($key);
			$instance->set($key,$val);
			$total++;
		}else{
		
		  if(!$instance->exists_key($key))
	      {
			$instance->set($key,$val);
			$total++;
		  }
		}
	}
	
	$instance->save();
	return $total;
}

/*
Retorna um array com todas as funções que contenha uma determinada permissão
*/
function get_roles_permission($value)
{
	 $file = 'config/access/access.php';
	 $access = new ReadArray($file);
	 $access = $access->gets();
	  
	 $contents = array();
	 foreach($access as $role=>$permissions)
	 {
		 foreach($permissions as $permission)
		 {
			if($value==$permission){
				if(!in_array($role,$contents)){
				  $contents[] = $role;	
				}
			} 
		 }
	 }
	return $contents;  
}
/*
Retorna um array com todas as permissões que contenha uma determinada função
*/
function get_permissions_role($role=null)
{
	 $file = 'config/access/access.php';
	 $access = new ReadArray($file);
	 return $role ? $access->get($role) : $access->gets();
}

/*Renderiza um componente dentro de um template*/
function component($component,$scope=null)
{
		  $component = 'np_'.str_ireplace('-','_',$component);
		  if(function_exists($component))
		  {
			return call_user_func($component,$scope);
		  }
}

/*Define um valor padrão para uma variável do tipo nula*/
function isnull($var,$default){
	return is_null($var) ? $default : $var;
}
/*Define um valor padrão para uma variável do tipo não nula*/
function notnull($var,$default){
	return !is_null($var) ? $default : $var;
}


function search($prefix=null)
{
  $prefix = notnull($prefix,$prefix.'.');
  
  $gets = $_GET;

  $query = null;
  $nst = isset($gets['nst']) ? $gets['nst'] : 'e';
  
  if(isset($gets['nsn']) && isset($gets['nsv']))
  {
	$key = $gets['nsn'];
    $val = $gets['nsv'];	 
    
    if(strlen(trim($val)) >= 1 || $nst == 'v' ||  $nst == 'nv')
	{
		if($nst == 'ne'){
			$val = is_numeric($val) ? $val : "'{$val}'";
			$query .= " {$prefix}{$key} != {$val} AND";
		}elseif($nst == 'c' && !is_numeric($val)){
			$query .= " {$prefix}{$key} LIKE '%{$val}%' AND";
		}elseif($nst == 'nc' && !is_numeric($val)){
			$query .= " {$prefix}{$key} NOT LIKE '%{$val}%' AND";
		}elseif($nst == 'cc' && !is_numeric($val)){
			$query .= " {$prefix}{$key} LIKE '{$val}%' AND";
		}elseif($nst == 'tc' && !is_numeric($val)){
			$query .= " {$prefix}{$key} LIKE '%{$val}' AND";
		}elseif($nst == 'mi' && is_numeric($val)){
			$val = floatval($val);
			$query .= " {$prefix}{$key} < {$val} AND";
		}elseif($nst == 'mx' && is_numeric($val)){
			$val = floatval($val);
			$query .= " {$prefix}{$key} > {$val} AND";
		}elseif($nst == 'mai' && is_numeric($val)){
			$val = floatval($val);
			$query .= " {$prefix}{$key} >= {$val} AND";
		}
		elseif($nst == 'mii' && is_numeric($val)){
			$val = floatval($val);
			$query .= " {$prefix}{$key} <= {$val} AND";
		}
		elseif($nst == 'v'){
			$query .= " {$prefix}{$key} IS NULL AND";
		}elseif($nst == 'nv'){
			$query .= " {$prefix}{$key} IS NOT NULL AND";
		}else{
			$val = is_numeric($val) ? $val : "'{$val}'";
			$query .= " {$prefix}{$key} = {$val} AND";
		}
	} 
  }
  
  if(isset($gets['nsb']) && isset($gets['nsi']) && isset($gets['nse']))
  {
	$key = $gets['nsb'];
    $val1 = $gets['nsi'];	
    $val2 = $gets['nse'];	
    
    if(strlen(trim($val1)) >= 1 && strlen(trim($val1)) >= 1)
	{
		$val1 = is_numeric($val1) ? $val1 : "'{$val1}'";
		$val2 = is_numeric($val2) ? $val2 : "'{$val2}'";
		$query .= " {$prefix}{$key} BETWEEN {$val1} AND {$val2} AND ";
	} 
  }
  $query = substr(trim($query),0,-3);
  return $query;  
}

function query_pre_rows($rows,$prefix=null)
{
	$prefix = trim($prefix);
	$string = null;
	foreach($rows as $row){
		$row = trim($row);
		$string .= "{$prefix}.{$row} AS {$prefix}_{$row},";
	}
	$string = substr($string,0,-1);
	return $string;
}
/*Transforma um array em uma string*/
function to_string($array,$key=null)
{
	$string = null;
	if(is_array($array)){
	foreach($array as $value){
		$value = (is_array($value) && !is_null($key)) ? $value[$key] : $value;
		$string .= $value;
	 }
	}else{
		$string = $array;
	}
	return $string;
}

/*Faz uma requisção a uma API*/
function response($args){
			$http = $args['url'];
			
			$headers = isset($args['header']) ? $args['header'] : array();
			$method =  isset($args['method']) ? $args['method'] : 'POST';
			
			$json_decode =  isset($args['array']) && is_bool($args['array']) ? $args['array'] : false;
			$ignore_errors =  isset($args['ignore_errors']) && is_bool($args['ignore_errors']) ? $args['ignore_errors'] : true;
			
			
			if(isset($args['body']) && is_array($args['body']))
			{
			    $body = http_build_query($args['body']);
			}else{
				$body = isset($args['body']) ? $args['body'] : null;
			}
			
            $header_values = null;
            foreach($headers as $key=>$val)
			{
				$header_values .= "{$key}: {$val}\r\n";
			}  
          
            $context = stream_context_create(array(
                'http' => array(
				    'ignore_errors' => $ignore_errors,
                    'method' => $method,                    
                    'header' => $header_values,
                    'content'=>$body					
                )
             ));
			
            $contents = file_get_contents($http, null, $context); 
			
            if($json_decode){ $contents = json_decode($contents, true); }			
            
			
            return (object) array(
			 'status'=>response_status_code($http_response_header),
			 'results'=>$contents
			);
}

/*Pega o código de retorno de uma req HTTP*/
function response_status_code($status)
{
	$status = $status[0];
	$status = explode(' ',$status);
	$status = $status[1];
	return (int) $status;
}

function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/*Pega a primeira letra ou segunda*/
function np_lf($string,$qtd=1){
	$string = trim($string);
	$string = $string ? $string : 'z';
	$string = substr($string,0,1);
	return strtoupper($string);
}

/*Converte uma string de URL separada por vigula*/
function qsh_e_deli($string,$deli=',')
{
	return str_ireplace(',','-',$string);
}

function qsh_d_deli($string,$deli=',')
{
	return str_ireplace('-',',',$string);
}

/*Converte string do tipo query string em array do PHP*/
function qsh_to_array($string,$deli='&')
{
	  $string = urldecode($string);
	  $x = explode($deli, $string);
	  $arr = array();
	  foreach($x as $data)
	  {
		$data = explode('=', $data);
		$arr[$data[0]] = isset($data[1]) ? $data[1] : null;
	  }
	  return $arr;
}

/*Modo da aplicação*/

function np_info_status($class=null)
{
	$name = auth() ? "Olá <b>".user_name()."</b>!" : null;
	$info = "[Versão - ".NP_VERSION."] {$name} Você está em um ambiente de ";
	$style = null;
	switch(NP_STATUS){
		case 'dev' : 
		  $info .= 'desenvolvimento.';
		  $style = 'background-color:yellow;color:red';
		break;
		case 'test' : 
		  $info .= 'testes.';
		  $style = 'background-color:blue;color:white';
		break;
		
		case 'approved' : 
		  $info .= 'homologação.';
		break;
		default : $info = null;
	}
	
	 if(!is_null($info)) return "<div style='{$style}' class='{$class}'><p>{$info}</p></div>";
}

/*Função para debug*/
function debug($x)
{
	if(is_array($x))
	{
		$x = implode(',',$x);
	}
	return $x;
}

/*Função para debug em alert*/
function debug_alert($x)
{
	hello(alert(debug($x),'info'));
}
/*Calcula a porcentagem de um número em relação ao outro*/
function calc_p($t,$v)
{
  if($t > 0) 
	  return ($v/$t) . 100;
  else return 0;
}

function get_menu_modules($menu_painel=false)
{
	$instance = $GLOBALS['np_instance_of_json_mods'];
	$instance = $instance->gets();
	$menus = array();
	foreach($instance as $key=>$values)
	{
	   extract($values);
       if(strtolower($key) != strtolower('Painel')){
		if($status == 'active'){
			if($menu_painel){
				array_push($menus,"{$route}|{$name}");
			}else{
				$menus[$route] = $name;
			}
		  }
       }
	}
	return $menus;
}

function np_painel_menu()
{
	if(Param::getIndex(0,'dashboard'))
	{
		$path = 'modules/Painel/menu.json';
	}else
	{
	   $path = Param::first();
	   $path = ucfirst($path);
	   $path = "modules/{$path}/dashboard.json";	
	}
	
	$instance = new Json($path);
	
	$first = Param::first();
	$instance = $instance->get($first);
	
    $html = null;
	
	
	
	if($instance){
	foreach($instance as $key=>$values)
	{
		$display = null;
		if(Param::getIndex(1,$key))
		{
			$display = 'display:block';
		}
		
		
	  //qsh_to_array($string)
	 if(is_array($values))
	 {
		$id = str_to_sanitize($key,false);
		
		$name = explode('|',$key);
		$icon = isset($name[2]) ? $name[2] : 'link';
		$name = isset($name[1]) ? $name[1] : $key;
		
		
		$html .= "<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#{$id}' aria-expanded='false' aria-controls='{$id}'>
         <div class='np-nav-link-icon'><i class='material-icons'>{$icon}</i></div>
		 {$name}<div class='np-sidenav-collapse-arrow'><i class='material-icons'>arrow_drop_down</i></div></a>"; 
	    
		$html .= "<div class='collapse' id='{$id}' style='{$display}' aria-labelledby='headingOne' data-bs-parent='#sidenavAccordion'>
        <nav class='np-sidenav-menu-nested nav'>";
		
		foreach($values as $link)
		{
			$link = qsh_to_array($link,'|'); 
			$icon = isset($link['icon']) ? $link['icon'] : 'more_vert';
			$route = str_ireplace(':','=',$link['route']);
			$route = url($first.'/'.$route);
			$html .= "<a class='nav-link' href='{$route}'>
			<i class='material-icons'>{$icon}</i>
			{$link['text']}</a>";
		}
		
       $html .= "</nav></div>";
		
		
	 }else{
		 
		$link = qsh_to_array($values,'|'); 
		
		$icon = isset($link['icon']) ? $link['icon'] : 'more';
		
		$route = strlen(trim($link['route'])) > 1 ?  $first.'/'.$link['route'] : $first;
		$route = str_ireplace(':','=',$route);
		$route = url($route);
		
		
		
		$html .= "<a class='nav-link' href='{$route}'>
                   <div class='np-nav-link-icon'>
				   <i class='material-icons'>{$icon}</i></div>
                   {$link['text']}
                  </a>";
		
	    }
	  }
	}
	return $html;
}

/*Carrega as informações de todos os módulos ativos*/
function get_info_modules($statusx='active')
{
	$instance = $GLOBALS['np_instance_of_json_mods'];
	$mods = $instance->gets();
	$menus = array();
	foreach($mods as $key=>$values)
	{
	   extract($values);
       if(strtolower($key) != strtolower('Painel'))
	   {
		if($status == $statusx)
		    {
				$menus[$route] = $instance->revert_utf8($values,true);
			}
		  
       }
	}
	return $menus;
}

/*Função para concatenar texto para exportação em PDF*/
function np_pdf_start(){
	if(!isset($_SESSION)) session_start();
	if(!isset($_SESSION['np_pdf_export'])) $_SESSION['np_pdf_export'] = array();
}

function np_pdf_set($key,$value=null){
    np_pdf_start();
	if(!array_key_exists($key,$_SESSION['np_pdf_export'])){
		$_SESSION['np_pdf_export'][$key] = $value;
	}
}
/*Transofma string do tipo date em um inteiro*/
function intdate($date=null)
{
   $date = is_null($date) ? date('Ymd') : $date;
   $date = str_ireplace(['-','/','.'],'',$date);
   return intval($date);
}

function np_pdf_get($key=null,$css=true){
	np_pdf_start();
	$values = $_SESSION['np_pdf_export'];
	
	$keys = "<link rel='stylesheet' type='text/css' href='".asset('app/css/themes/vertical-menu-nav-dark-template/materialize.css')."'>
	<style>.np-hide-element{display:none !important;}</style>";
	
	$keys = $css ? $keys : null;
	
	if(is_null($key)){
		foreach($values as $value){
			$keys .= $value;
		}
		return $keys;
	}else{
		if(array_key_exists($key,$_SESSION['np_pdf_export'])){
		   $keys .= $_SESSION['np_pdf_export'][$key];
		   return $keys;
		}
	}
}

function np_pdf_del($key=null){
	np_pdf_start();
	if(is_null($key)){
		unset($_SESSION['np_pdf_export']);
	}else{
		if(array_key_exists($key,$_SESSION['np_pdf_export'])){
			unset($_SESSION['np_pdf_export'][$key]);
		}
	}
}

/**/
function errors(){
	
	if (!isset($_SESSION)) session_start();
	
	if(isset($_SESSION['np_errors'])){
		return $_SESSION['np_errors'];
	}
}

function errors_message(){
	if(errors()){
		$text = implode(' e ',errors());
		$text = strtolower($text);
		$text = str_ireplace('. ',' ',$text);
		$text = ucfirst($text);
		$text = $text.'.';
		$text = str_ireplace('..','.',$text);
		return $text;
	}
}

/*Formatção para moedas*/
function format_money_sup($float,$sim='R$',$span='teal-text',$sup='teal-text'){
	 $float = floatval($float);
	 return '<span class="'.$span.'">'.$sim.' '.number_format($float, 2, '<sup class="'.$sup.'">,', '.').'</sup></span>'; 
}

function format_money($float,$sim='R$',$sep=','){
	 return $sim.' '.number_format($float, 2, $sep, '.'); 
}

function format_brl($float){
	 if($float != 0){
		  return number_format($float, 2, ',', '.');
	 }else return '-';
}

function theme($color=null,$colors=array()){
     if(in_array(NP_THEME,$colors)){
		return $color;
	 }else{
		return NP_THEME;
	 }
}

function theme_null($color=null,$colors=array()){
     if(in_array(NP_THEME,$colors)){
		return $color;
	 }
}

function route_first()
{
	return Param::first();
}

function db_table($tableName)
{
	return DB::table($tableName);
}


/*Chama uma rota do tipo GET*/
function route($route, $controller, $args = null)
{
	Route::get($route, $controller, $args);
}

/*Chama uma rota do tipo POST*/
function post_route($route, $controller, $args = null)
{
	Route::post($route, $controller, $args);
}

/*Chama uma rota do tipo PUT*/
function put_route($route, $controller, $args = null)
{
	Route::put($route, $controller, $args);
}

/*Chama uma rota do tipo DELETE*/
function delete_route($route, $controller, $args = null)
{
	Route::delete($route, $controller, $args);
}

/*Chama uma rota com todos os tipos de verbos*/
function any_route($route, $controller, $args = null)
{
	Route::any($route, $controller, $args);
}

/*Cria recuros de rota*/
function resources_route($route, $controller, $args = null)
{
	Route::resources($route, $controller, $args);
}

/*Retorna a instancia da classe Request. Com essa função não é necessário instanciar uma nova classe Request na aplicação*/
function get_instance()
{
	return $GLOBALS['np_instance_of_request'];
}

/*Função para retornar todos os índices de request. Se informado uma chave, a função só retornará a chave informada no parâmetro.*/
function request($key = null)
{
	return is_null($key) ? get_instance()->all() : get_instance()->get($key);
}

/*Extrai os ID's das linhas colocando em um novo array com ID e value. Essa função é geralmente usada nos retornos do banco de dados*/
function id_value($array, $val='name', $id = 'id')
{
	$x = array();
	if (count($array) > 0) {
		foreach ($array as $row) {
			$x[$row[$id]] = $row[$val];
		}
		return $x;
	} else return $x;
}

/*Impera sobre resultados de um array,geralmente usado com array associativos de uma query SQL
$map = array que será imperado
$ind = índice
$fun = Callback
*/
function np_map($map,$ind,$fun){
	  foreach($map as $i=>$v)
	  {
		  if(is_callable($fun))
		  {
			$map[$i][$ind] = call_user_func($fun,$map[$i][$ind]);  
		  }
	  }
	    return $map;
}

/*Retonar o id numerico do recurso*/
function get_id()
{
	$uri = $GLOBALS['np_instance_of_uri'];
	$uri = $uri->uri();
	$route = explode('/', $uri);
	$count = count($route) - 1;

	if ($route[$count] == 'edit' && isset($route[$count - 1]))
		return is_numeric($route[$count - 1]) ? $route[$count - 1] : false;
	else return is_numeric($route[$count]) ? $route[$count] : false;
}

/*Função para pegar um request  especifico da aplicação*/
function get($x, $dafault = null)
{
	$instance = $GLOBALS['np_instance_of_request'];
	return $instance->get($x, $dafault);
}

function get_search(){
	$search = get('search');
	$search = ($search != 'search' && !is_null($search)) ? $search : false;
	return $search;
}

/*Função para pegar todos os requests da aplicação*/
function get_all($except = null)
{
	$instance = $GLOBALS['np_instance_of_request'];
	return $instance->all($except);
}

/*Função para verficar se uma variável request existe*/
function has_get($x)
{
	$instance = $GLOBALS['np_instance_of_request'];
	return $instance->has($x);
}

/*Função para renderização de arquivos de visualização da camada VIEW*/
function view($file, $scope = null)
{
	$instance = $GLOBALS['np_instance_of_view'];
	ob_start();
	$instance->render($file, $scope);	
	$view = ob_get_clean();
	return $view;
}

function template($html,$np_scope=null){
	
	$np_scope = !is_null($np_scope) && is_array($np_scope) ? $np_scope : array();
	extract($np_scope);
	ob_start();

    $var_html_open = 'echo "<div>"; ';
	$var_html_end = 'echo " </div>";';
    $html = $var_html_open.$html.$var_html_end;
    eval($html);	
	$template = ob_get_clean();
	return $template;
}

/*Função para verificar se um token crsf de sessão existe. Caso exista, a função retornará o token no formato de string, caso contário, a função retonará falso*/
//{{csrf_field()}}
function csrf_token()
{

	if (!isset($_SESSION)) session_start();
	$csrf = isset($_SESSION['np_csrf_token']) ? $_SESSION['np_csrf_token'] : false;
	return $csrf;
}

/*Função para validar token de sessão*/
function csrf_check($token)
{
	return (csrf_token() == $token) ? true : false;
}

/*Função para verfificar se um  usuário está autenticado. Se informado o tipo de usuário no parametro, a função irá verficar se o usuário está autenticado e se ele pertence ao tipo informado no parametro da função. Pode ser informado um array do tipo vetor com os nomes das função*/
function auth($role = null)
{
	return call_user_func('Nopadi\Http\Auth::check', $role);
}

/*Retorna a URL(base) da aplicação*/
function url($uri = null)
{
	if('#' == substr($uri,0,1))
	{
		return $uri;
	}
	elseif('https:' == strtolower(substr($uri,0,6)) || 'http:' == strtolower(substr($uri,0,5)))
	{
		return $uri;
	}
	else
	{
		$x = $GLOBALS['np_instance_of_uri'];
	    $uri = (is_null($uri) || $uri == '/') ? null : $uri;
	    return $x->base() . $uri;
	}
}

function href($uri = null){
	if('@show:' == strtolower(substr($uri,0,6)))
	{
		$id = substr($uri,6);
		return 'onclick="document.getElementById(\''.$id.'\').style.display=\'block\'"';
	}
	elseif('@hide:' == strtolower(substr($uri,0,6)))
	{
		$id = substr($uri,6);
		return 'onclick="document.getElementById(\''.$id.'\').style.display=\'none\'"';
	}
	elseif('@id:' == strtolower(substr($uri,0,4)))
	{
		$id = substr($uri,4);
		return 'id="'.$id.'"';
	}
	else{
		return 'href="'.url($uri).'"';
	}
}

/*Retorna os gets(querys) da URL para aplicar um filtro nos botões de paginação*/
function pag_filter($pag)
{
	$x = $GLOBALS['np_instance_of_uri'];
	$uri = explode('?', $x->uri());
	$uri = isset($uri[1]) ? $uri[1] : false;
	
	$arr1 = array('&0','&1','&2','&3','&4','&5','&6','&7','&8','&9');
    $link = null;
	if ($uri)
	{
		$uri = preg_replace("/(page=[0-9]+)/simU", '', $uri);
		$uri = str_ireplace(['&&&', '&&'], '&', $pag . '&' . $uri);
		$uri = str_ireplace($arr1, '',$uri);
		$link = $uri;
	} else{ $link = $pag; }
	
	if(substr($link,-1) == '&'){ $link = substr($link,0,-1); }
	return $link;
}
/*Obtem a URI atual da página ou rota*/
function get_uri($query=true)
{
	$x = $GLOBALS['np_instance_of_uri'];
	$x = $x->uri();
	
	if($query){
		return $x;
	}else{
		$query = explode('?',$x);
		return $query[0];
	}
}

/*Define um valor padrão se o campo for nulo ou vazio*/
function if_null($value,$default=""){
	return is_null($value) || empty($value) || $value == "" ? $default : $value; 
}


/*verfifica se a URL atual pertence a uma API*/
function is_api($api = 'api')
{
	$route = Param::first();
	if($route == $api) return true;
	else return false;
}
function is_method($method='get')
{
	return Request::isMethod($method);
}

/*Verifica se está na rota atual. Se o segundo parametro for 'true', a função irá ignorar a query na URL*/
function is_url($route = null, $ignoreType = false)
{
	if($route == '/'){ $route = null; } 
	
	$uri = $GLOBALS['np_instance_of_uri'];
	$url = $uri->base();
	$uri = $uri->uri();

	/*Inicio do Código para aceitar o parâmetro 'type'*/
	$route_type = false;
	$uri_type = false;
	$param_route_type = explode('?type=', $route);

	if (isset($param_route_type[1])) {
		$route = $param_route_type[0] . '/type-' . $param_route_type[1];
		$route_type = true;
	}

	$type = $GLOBALS['np_instance_of_request'];
	$type  = $type->get('type') ? $type->get('type') : false;

	if ($type && $route_type) {

		$param_uri = explode('?', $uri);
		$param_uri = $param_uri[0];

		$uri_type = true;

		$uri = $param_uri . '/type-' . $type;
	}

	if ($ignoreType) {

		$route = explode('?', $route);
		$route = $route[0];

		$uri = explode('?', $uri);
		$uri = $uri[0];
	}

	/*Fim do Código para aceitar o parâmetro 'type'*/

	if (substr($route, 0, 4) != 'http') {

		$route = str_ireplace('.', '/', $route);
		$route = ($route == '/' || is_null($route)) ? $uri : $url . $route;

		$route = str_ireplace('/{loop}', '{loop}', $route);
		$route = str_ireplace('/', '\/', $route);
		$route = str_ireplace(array('{id}', '{int}'), '([0-9]+)', $route);
		$route = str_ireplace('{string}', '([A-Za-zÀ-ú0-9\.\-\_]+)', $route);
		$route  = str_ireplace('{letter}', '([A-Za-z]+)', $route);
		$route = str_ireplace('{loop}', '(\/[A-Za-zÀ-ú0-9\.\-\_]+)*', $route);
		/*ira aplicar em tudo, menos na api*/
		$route = str_ireplace('{!api}', '([^api]+)', $route);

      
		if(preg_match("/^{$route}$/i", $uri)) return true;
		else return false;
	} else {
		if ($route == $uri) return true;
		else return false;
	}
}
/*Identifica se existe ocorrência no caminho especificado*/
function has_path($route = null,$queryIgnore=true)
{
	if($route == '/'){ $route = null; } 
	
	if(!is_null($route))
	{
      $uri = Param::path($queryIgnore);
      $route = str_ireplace('/','\/',$route);
	  $route = str_ireplace('?','\?',$route);
      if(preg_match("/{$route}/i", $uri)) return true;
      else return false;  	  	
	}else{
		return false;
	}
}

/*Redireciona o usuário para uma URL especifica informada*/
function to_url($to = null)
{
	$base = $GLOBALS['np_instance_of_uri'];
	$base = $base->base();
	$to = ($to == '/') ? null : $to;
	$base = $base . $to;
	header('Location:' . $base);
}

function param_first()
{
	return call_user_func('Nopadi\Http\Param::first');
}

/*Retorna os parâmetros das rotas*/
function param($key)
{
	return call_user_func('Nopadi\Http\Param::get', $key);
}
/*Retorna os parâmetros das rotas se for inteiro*/
function int_param($key)
{
	return call_user_func('Nopadi\Http\Param::int', $key);
}
/*Retorna os parâmetros das rotas se for float*/
function float_param($key)
{
	return call_user_func('Nopadi\Http\Param::float', $key);
}
/*Verifica se o nome do parâmetro da rota existe e qual é o seu valor*/
function is_param($key,$val)
{
	return call_user_func('Nopadi\Http\Param::is', $key,$val);
}

function user_set($key,$value=null)
{
		if(Auth::check()){
			if(isset($_SESSION['np_user_logged'][$key])) $_SESSION['np_user_logged'][$key] = $value; 
		}
}

/*Retorna os dados de sessão aberta do usuário*/
function user($role = null)
{
	return call_user_func('Nopadi\Http\Auth::user', $role);
}
/*Retorna todos os dados dos usuários no formato de array(associativo)*/
function user_all($role = null)
{
	return user($role)->all;
}

/*Retorna o nome da função/tipo do usuário da sessão atual*/
function user_role()
{
	return user()->role;
}

/*Retorna o id do usuário da sessão atual*/
function user_id()
{
	if(user()->id) 
		return user()->id;
	else 
		return 0;
}
/*Retorna o nome do usuário da sessão atual*/
function user_name()
{
	if(user()->name) 
		return user()->name;
	else 
		return 0;
}
/*Retorna o primeiro nome do usuário da sessão atual*/
function user_first($name=null)
{
	$name = is_null($name) ? user_name() : $name;
	if($name)
	{
		$name = explode(' ',$name);
        return $name[0];
	}
}

/*Retorna o caminho em URL da imagem do usuário da sessão atual*/
function user_image($path = null)
{
	$uri = $GLOBALS['np_instance_of_uri'];
	
	$path = is_null($path) ? user()->image : $path;

	$image = $uri->local(dir_public() . $path);

	if (file_exists($image) && !is_null($path)) {
		return $uri->base() . $path;
	}
	return false;
}

/*Retorna o nome do diretório publico (public_html/ ou public/)da aplicação*/
function dir_public()
{
	$uri = $GLOBALS['np_instance_of_uri'];
	return is_dir($uri->local("public_html/")) ? "public_html/" : "public/";
}

/*Remove a imagem de usuário da sessão atual*/
function user_image_remove($path = null)
{
	$uri = $GLOBALS['np_instance_of_uri'];
	$path = is_null($path) ? user()->image : $path;

	$image = $uri->local(dir_public() . $path);
	$public = substr($image,-7,8);

	$public_html = substr($image,-12,12);
	if($public != 'public/' && $public_html != 'public_html/'){
		if (file_exists($image) && !is_null(user_image())) {
		if (unlink($image)) return true;
		else return false;
	}
	return false;
	}else return false;
}

/*Retorna o caminho base da pasta public/public_html do site*/
function asset($path = null)
{
	$uri = $GLOBALS['np_instance_of_uri'];
	return $uri->asset($path);
}
function options_checkbox($name,$items,$checked=false,$class='')
{
	$checkbox = null;
	$checked = $checked ? 'checked="checked"' : null;
	if(count($items) > 0)
	{
		foreach($items as $key=>$val){
		    $checkbox .= "<div class='{$class}'>
			        <div class='form-check'>
                       <input name='{$name}[]' class='form-check-input' type='checkbox' value='{$key}' id='flexCheck{$name}{$key}' {$checked}>
                     <label class='form-check-label' for='flexCheck{$name}{$key}'>{$val}</label></div></div>";
		}
	}
	return $checkbox;
}


function options_string($items)
{
	$ids = null;
	if(is_array($items)){
	if(count($items) > 0)
	{
		foreach($items as $key)
		{
			$ids .= $key.',';
		}
		$ids = trim($ids);
		$ids = substr($ids,0,-1);
	}}
	return $ids;
}

function options_post($name)
{
	if(isset($_POST)){
	  if(isset($_POST[$name])){
		   return $_POST[$name];
	   }	
	}
}

function options_get($name)
{
	if(isset($_GET)){
	  if(isset($_GET[$name])){
		   return $_GET[$name];
	   }	
	}
}


/*Transformação um array associativo em options do HTML. O segundo parametro é a chave(ínice do array) do elemento option que está selecionado, retonando assim, um valor já checado.*/
function options($array = null, $check = null)
{
	$option = null;
	foreach ($array as $key => $val) {
		if ($key == $check) {
			$option .=  '<option value="' . $key . '" selected>' . $val . '</option>';
		} else {
			$option .=  '<option value="' . $key . '">' . $val . '</option>';
		}
	}
	return $option;
}

/*Declara uma variável com escopo global*/
function set_var($name,$value=null) 
{
    $GLOBALS[$name] = $value;
	return $value;
}

/*Acessa uma variável com escopo global*/
function get_var($name,$default=null)
{
	$name = isset($GLOBALS[$name]) ? $GLOBALS[$name] : $default;
    return $name;	
}

function get_var_i($name,$value=0)
{
	if(isset($GLOBALS[$name]))
	{
	   $GLOBALS[$name] += $value;	
	}
}

/*Converte um array indexado e sequencial em uma lista onde os índices serão iguais aos valores*/
function to_list($array)
{
	$i = 0;
	$array = is_array($array) ? $array : array($array);
	$list = true;
	$copy_list = array();
	foreach($array as $key=>$val)
	{
	  	if(is_numeric($key) && $key == $i && $list == true)
		{
		  $copy_list[$val] = $val;	
		  $i++;
		}else{
		  $list = false; 
		}
	}
	return $list ? $copy_list : $array;
}

function delete_filters(){
	setcookie('np_form_filters', '');  	
}
function set_filters($array_assoc)
{
	$array_assoc = serialize($array_assoc);
	setcookie('np_form_filters',$array_assoc);
}

function get_filters()
{
	$cookies = array();
	if(isset($_COOKIE['np_form_filters']))
	{
		$cookies = unserialize($_COOKIE['np_form_filters']);
	}
	return $cookies;
}

function has_filter($name,$arr=null)
{
	$value = null;
	$filters = is_null($arr) ? get_filters() : $arr;
    if(array_key_exists($name,$filters))
	{
		$value = $filters[$name];
	}
	return $value;
}

function input_filter($name,$value=null)
{
  $options = null;
  $class = 'form-control';
  
  $request = get_filters();
  
  $name = explode(':',$name);
  
  $type = isset($name[1]) ? $name[0] : 'text';  
  $default = isset($name[2]) ? $name[2] : null; 
  $min_max = isset($name[3]) ? $name[3] : null;
  
  
  if($type == 'list' && !is_array($value))
  {
	  $value = array($value);
  }
  
  $min = null;
  $max = null;
  
  if(!is_null($min_max))
  {
	$min_max = explode('-',$min_max);
    $min = "min='{$min_max[0]}'"; 
    $max = isset($min_max[1]) ? "max='{$min_max[0]}'" : null; 	
  }
  
  $name = isset($name[1]) ? $name[1] : $name[0];
  
  if(has_filter($name,$request))
  {
	$default = has_filter($name,$request);  
  }

  if(is_array($value))
  {  
     $type = 'list';
	 $value = to_list($value);
	 foreach($value as $key=>$val)
	 {
		$selected = ((!is_null($default)) && (strtolower($key) == strtolower($default))) ? 'selected' : null;
		$options .= "<option value='{$key}' {$selected}>{$val}</option>"; 
	 } 
  }else{
	   $value = !is_null($default) ? $default : $value;  
  }	
	
  switch($type)
  {
	case 'email' : 
	   $input = "<input type='email' class='{$class}' name='{$name}' value='{$value}'>";
	break;
	
	case 'password' : 
	   $input = "<input type='password' class='{$class}' name='{$name}' value='{$value}'>";
	break;
	
	case 'number' : 
	   $input = "<input type='number' class='{$class}' name='{$name}' value='{$value}' {$min} {$max}>";
	break;
	
	case 'date' : 
	   $input = "<input type='date' class='{$class}' name='{$name}' value='{$value}'>";
	break;
	
	case 'list' : 
	   $input = "<select name='{$name}' class='{$class} browser-default'>{$options}</select>";
	break;
	
	default : 
	
	 $input = "<input type='text' class='{$class}' name='{$name}' value='{$value}'>";
  }
  return $input;
}


/*Mapea um array associativo e retonando somente o valor do elemento que teve a sua chave informada na segundo parêmetro*/
function options_text($array = null, $check = null)
{
	$option = null;
	foreach ($array as $key => $val) {
		if ($key == $check) {
			$option = $val;
		} 
	}
	return $option;
}

/*Retonar somente um índice especifco do array ou o valor da chave informada no caso de não existir o índice no array especificado no segundo parâmetro*/
function array_one($key, $array)
{
	$array = array_key_exists($key, $array) ? $array[$key] : $key;
	return $array;
}

/*Retorna uma tradução de um item formatado ou de uma variável. Para que a tradução seja realizada é necessário colocar no inicio da string o :*/
function text($value = null, $alert = null)
{
	$instance = $GLOBALS['np_instance_of_translater'];

	if (substr($value, 0, 1) == ':') {
		$value = trim(str_ireplace(':', '', $value));
		$value = $instance->text($value);
	} else {
		$value = str_ireplace('!:', ':', $value);
	}

	if (is_object($value)) $value = get_object_vars($value);
	if (is_array($value)) $value = implode(', ', $value);

	if (!is_null($alert)) {

		$hello = $GLOBALS['np_instance_of_json'];
		$alert = $hello->val('hello', $alert);

        $alert = explode(':',$alert);
		$class_content = $alert[0]; 
		$class_text = isset($alert[1]) ? $alert[1] : null; 
		
		$value = '<div class="' . $class_content . '"><div class="'.$class_text.'" role="alert">' . $value . '</div></div>';
	}
	return $value;
}

function lang($lang){
	return str_ireplace(['[',']'],'',text(':'.$lang));	
}

/*Similar a text, com a diferença que essa função imprime o valor em tela*/
function hello($value = null, $alert = null)
{
	echo text($value, $alert);
}

/*Similar a text, com a diferença que a tradução é feito em um array*/
function array_text($array)
{
	foreach ($array as $key => $val) {
		$array[$key] = text($val);
	}
	return $array;
}

/*Carrega os arquivos de css da aplicação que estão configurados no diretório config/app/hello.json*/
function style($only = null)
{
	$instance = $GLOBALS['np_instance_of_json'];
	$uri = $GLOBALS['np_instance_of_uri'];

	$style = is_null($only) ? $instance->val('styles') : [$instance->val('styles', $only)];
	$css = null;

	if (is_array($style)) {
		foreach ($style as $key => $val) {

			$val = trim($val);

			if (substr($val, -4, 4) != '.css') $val = $val . '.css';

			if (substr($val, 0, 4) != 'http') {
				$css .= '<link rel="stylesheet" href="' . $uri->asset('css/' . $val) . '">';
			} else {
				$css .= '<link rel="stylesheet" href="' . $val . '">';
			}
		}
	}
	return $css;
}

/*Função para saída do tipo json. Essa função também declara um cabeçalho/header para o browser*/
function json($value = null) 
{
	if (is_numeric($value) || is_string($value)) {
		$value = array($value);
	} elseif (is_object($value)) {
		$value = get_object_vars($value);
	}
	header('Content-Type: application/json;charset=utf-8');
	echo json_encode($value);
}

function ijson($value = null)
{
	if (is_numeric($value) || is_string($value)) {
		$value = array($value);
	} elseif (is_object($value)) {
		$value = get_object_vars($value);
	}
	return json_encode($value);
}

/*Carrega os arquivos de js da aplicação que estão configurados no diretório config/app/hello.json*/
function script($only = null)
{
	$uri = $GLOBALS['np_instance_of_uri'];
	$instance = $GLOBALS['np_instance_of_json'];

	$script = is_null($only) ? $instance->val('scripts') : [$instance->val('scripts', $only)];

	$js = null;

	if (is_array($script)) {
		foreach ($script as $key => $val) {

			$val = trim($val);

			if (substr($val, -3, 3) != '.js') $val = $val . '.js';

			if (substr($val, 0, 4) != 'http') {
				$js .= '<script src="' . $uri->asset('js/' . $val) . '"></script>';
			} else {
				$js .= '<script src="' . $val . '"></script>';
			}
		}
	}
	return $js;
}

/*Inicio das funções para formatação*/

function revert_utf8($string,$r=false)
{
	$c = array('À','Á','Ã','Â','É','Ê','Í','Ó','Õ','Ô','Ú','Ü','Ç','Ñ','à','á','ã','â','é','ê','í','ó','õ','ô','ú','ü','ç','ñ');
	
    $s = array('xx1x','xx2x','xx3x','xx4x','xx5x','xx6x','xx7x','xx8x','xx9x','x1xx','x11x','x12x','x13x','x14x','x15x','x16x','x17x','x18x','x19x','x2xx','x21x','x22x','x23x','x24x','x25x','x26x','x27x','x28x');
	
	if($r){
		$string = str_replace($s,$c,$string);
	}else{
		$string = str_replace($c,$s,$string);
	}
  	return $string;
}


/*Transforma um id inteiro em código de letras fixo: OBS o número deve ser inteiro sem zero na frente*/
function id_to_code($code,$size=10)
{
	$code = (int) $code;
	$size_code = strlen($code);
	$zero_total =  $size - $size_code;
	$code = $code.'j'.str_repeat("0",$zero_total);
	
	$n = array('0','1','2','3','4','5','6','7','8','9');
	$l = array('f','g','b','z','h','s','w','q','n','k');
	
	$code = str_ireplace($n,$l,$code);
	return strtoupper($code);	
}

/*Transforma um "id code" em inteiro*/
function code_to_id($code)
{
	$code = strtoupper($code);
	$code = explode('J',$code);
	$code = $code[0];
	$l = array('f','g','b','z','h','s','w','q','n','k');
	$n = array('0','1','2','3','4','5','6','7','8','9');
	$code = str_ireplace($l,$n,$code);
	return (int) $code;	
}

/*Transforma em código para chaves em caracteres especiais*/
function str_to_code($strTitle,$m=false)
{
	 $acentos = array("á", "Á", "ã", "Ã", "â", "Â", "à", "À", "é", "É", "ê", "Ê", "è", "È", "í", "Í", "ó", "Ó", "õ", "Õ", "ò", "Ò", "ô", "Ô", "ú", "Ú", "ù", "Ù", "û", "Û", "ç", "Ç", "º", "ª");
	 $letras = array("a", "A", "a", "A", "a", "A", "a", "A", "e", "E", "e", "E", "e", "E", "i", "I", "o", "O", "o", "O", "o", "O", "o", "O", "u", "U", "u", "U", "u", "U", "c", "C", "o", "a");
	 
	  $strTitle = trim($strTitle);
	  $strTitle = str_ireplace($acentos, $letras, $strTitle);
	 
	  $strTitle = str_ireplace(' ','-', $strTitle);
	  $strTitle = str_ireplace('  ','-', $strTitle);
	  $strTitle = str_ireplace('-_','-', $strTitle);
	  $strTitle = str_ireplace('_-','_', $strTitle);
	  $strTitle = str_ireplace('__','_', $strTitle);
	  $strTitle = str_ireplace('___','_', $strTitle);
	  $strTitle = str_ireplace('--','-', $strTitle);
	  $strTitle = str_ireplace('---','-', $strTitle);
	  
	  return $m ? strtoupper($strTitle) : strtolower($strTitle);
}


/*Transforma uma string comum no formato URL*/
function str_url($strTitle, $ignorePonto = true)
{
	/* Remove pontos e underlines */
	$arrEncontrar = array(".", "_");
	$arrSubstituir = null;

	if ($ignorePonto == true) $strTitle = str_ireplace($arrEncontrar, $arrSubstituir, $strTitle);

	/* Caracteres minúsculos */
	$strTitle = strtolower($strTitle);
	/* Remove os acentos */
	$acentos = array("á", "Á", "ã", "Ã", "â", "Â", "à", "À", "é", "É", "ê", "Ê", "è", "È", "í", "Í", "ó", "Ó", "õ", "Õ", "ò", "Ò", "ô", "Ô", "ú", "Ú", "ù", "Ù", "û", "Û", "ç", "Ç", "º", "ª");
	$letras = array("a", "A", "a", "A", "a", "A", "a", "A", "e", "E", "e", "E", "e", "E", "i", "I", "o", "O", "o", "O", "o", "O", "o", "O", "u", "U", "u", "U", "u", "U", "c", "C", "o", "a");
	$strTitle = str_ireplace($acentos, $letras, $strTitle);
	$strTitle = preg_replace("/[^a-zA-Z0-9._$, ]/", "", $strTitle);
	$strTitle = iconv("UTF-8", "UTF-8//TRANSLIT", $strTitle);
	/* Remove espaços em branco*/
	$strTitle = strip_tags(trim($strTitle));
	$strTitle = str_ireplace(" ", "-", $strTitle);
	$strTitle = str_ireplace(array("-----", "----", "---", "--"), "-", $strTitle);
	return $strTitle;
}

function to_datetime($string,$final=true){
	if(strlen($string) == 10)
	{
		$final = $final ? '23:59:59' : '00:00:00';
		$string = $string.' '.$final;
	}
	return $string;
}

function to_datetime_pt_en($date=null,$d=null){
	
	if(strlen($date) > 9){
		
	  $date = explode('/',$date);
	  $y = $date[2]; 
	  $m = $date[1];
      $d = $date[0];	
	  return "{$y}-{$m}-{$d} 00:00:00";
	
	}else return $d;
}

/*Validar CPF ou CNPJ*/
function validate_cpf_cnpj($cpf_cnpj)
{
	return validate_cpf($cpf_cnpj) xor validate_cnpj($cpf_cnpj) ? true : false;
}

/*Validar CPF*/
function validate_cpf($cpf) {

    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}
/*Retira as máscara/formatação de uma string*/
function not_mask($str)
{
	$str = str_ireplace(' ','',$str);
	$str = str_ireplace('.','',$str);
	$str = str_ireplace(':','',$str);
	$str = str_ireplace('|','',$str);
	$str = str_ireplace(',','',$str);
	$str = str_ireplace(';','',$str);
	$str = str_ireplace(']','',$str);
	$str = str_ireplace('[','',$str);
	$str = str_ireplace('\\','',$str);
	$str = str_ireplace('/','',$str);
	$str = str_ireplace('-','',$str);
	$str = str_ireplace('_','',$str);
	return trim($str);
}

/*Validar CNPJ*/
function validate_cnpj($cnpj)
{
	$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
	
	// Valida tamanho
	if (strlen($cnpj) != 14)
		return false;

	// Verifica se todos os digitos são iguais
	if (preg_match('/(\d)\1{13}/', $cnpj))
		return false;	

	// Valida primeiro dígito verificador
	for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
	{
		$soma += $cnpj[$i] * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}

	$resto = $soma % 11;

	if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
		return false;

	// Valida segundo dígito verificador
	for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
	{
		$soma += $cnpj[$i] * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}

	$resto = $soma % 11;

	return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}
/*Formata CPF e CNPJ*/
function format_cnpj_cpf($value)
{
  $value = strlen($value) == 10 ? '0'.$value : $value;
  $value = strlen($value) == 13 ? '0'.$value : $value;
  $CPF_LENGTH = 11;
  $cnpj_cpf = preg_replace("/\D/", '', $value);
  
  if (strlen($cnpj_cpf) === $CPF_LENGTH) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
  } 
  
  return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
}

function format_next($list){
	
  $next = pag_filter($list->next);
  $previous = pag_filter($list->previous);
 
  $next = ($list->page == $list->total) ? null : $next;
  $previous = ($list->page == 1) ? null : $previous;
  
  $next = $next ? '<a href="'.$next.'"><i class="material-icons">chevron_right</i></a>' : '<a href="javascript:void(0)" class="link-disabled"><i class="material-icons">chevron_right</i></a>';
  $previous = $previous ? '<a href="'.$previous.'"><i class="material-icons purple-text">chevron_left</i></a>' : '<a href="javascript:void(0)" class="link-disabled"><i class="material-icons purple-text">chevron_left</i></a>';
  
  $list->total = $list->count == 0 ? 0 : $list->total;

   
  
  $text = $list->page == 1 && $list->total == 0 || $list->total == 0 ? '<span class="red-text">não há páginas para exibir.</span>' : '<b>'.$list->page.'</b> de '.$list->total;

   if($list->count == 0){  $previous = null; $next = null; }
  
  $html = '<div class="w-75 mx-auto d-flex justify-content-between align-align-items-center p-2">
   '.$previous.'
   <span>
       Total de registros: <b>'.$list->count.'</b> | Página: '.$text.'
   </span>
   '.$next.'
</div>';
return $html;
}

/*Função para formatar uma saída de acordo com o arquivo de tradução 'app.json'*/
function format($string, $format)
{
	$string = trim($string);
	$string = $format == 'date' ? substr($string,0,10) : $string;
	
	if($format == 'datetime' && strlen($string) == 10)
	{
		$string = $string.' 00:00:00';
	}
	
	$instance = $GLOBALS['np_instance_of_translater'];
	$format = $instance->val('function.format', $format);
	if ($format) {
		$format = explode('=', $format);
		$er = "/{$format[0]}/simU";
		$replace = isset($format[1]) ? $format[1] : null;
		$string = preg_replace($er, $replace, $string);
	}
	return $string;
}


/*Oculta uma parte do texto*/
function text_more($string,$qtd=150,$more='[...]'){
	return (strlen($string) > $qtd) ? substr($string,0,$qtd - 3).$more : $string; 
}
/*Serialize str*/
function str_to_sanitize($str,$space=true)
{
    $str = preg_replace('{\W}', '', preg_replace('{ +}', '_', strtr(
        utf8_decode(html_entity_decode($str)),
        utf8_decode('ÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ'),
        'AAAAEEIOOOUUCNaaaaeeiooouucn')));
		
	return $space ? str_ireplace('_',' ',$str) : $str;
}


function unicode_decode($str)
{
    $s1 = array('u00e1','u00e0','u00e2','u00e3','u00e4','u00c1','u00c0','u00c2','u00c3','u00c4','u00e9','u00e8','u00ea','u00ea','u00c9','u00c8','u00ca','u00cb','u00ed','u00ec','u00ee','u00ef','u00cd','u00cc','u00ce','u00cf','u00f3','u00f2','u00f4','u00f5','u00f6','u00d3','u00d2','u00d4','u00d5','u00d6','u00fa','u00f9','u00fb','u00fc','u00da','u00d9','u00db','u00e7','u00c7','u00f1','u00d1','u0026','u0027');

    $s2 = array('á','à','â','ã','ä','Á','À','Â','Ã','Ä','é','è','ê','ê','É','È','Ê','Ë','í','ì','î','ï','Í','Ì','Î','Ï','ó','ò','ô','õ','ö','Ó','Ò','Ô','Õ','Ö','ú','ù','û','ü','Ú','Ù','Û','ç','Ç','ñ','Ñ','&','\'');

    return str_ireplace($s1,$s2,$str);
}
