<?php
/*
Classe responsável pela compilação dos arquivos de visualizações
*/

namespace Nopadi\MVC;

use Exception;
use Nopadi\FS\Json;
use Nopadi\Http\URI;
use Nopadi\MVC\Form;
use Nopadi\Support\Translation;

class View extends Translation
{
	/*caminho dos templates*/
	protected $path;
	/*Caminho dos caches*/
	protected $cache =  'storage/cache/views';
	/*regra ER para trasnformação*/
	private $all =  "([\[\]\t\n\r\f\v\-A-Za-zÀ-ú0-9\s\{\} &,\_\$\.\"\'\:\|\(\)\+\-\*\%\/\!\?\>\<\=@#;]+)";
	/*armazena os dados do scope*/
	private $scope;
	/*armazena os comandos de componentização*/
	private $components;

	/*retonar o caminho de raz do site*/
	final public function local($local)
	{
		$config = new URI();
		return $config->local($local);
	}
	public static function FormReturn($x,$type)
	{
		if(is_string($x))
		{   $arr = array();
		    $arr['name'] = $x; 
			$arr['type'] = $type; 
			return $arr;
		}else{
			$x['type'] = $type; 
			return $x;
		 }
	}
	public function FormClose(){ return Form::close(); }
	public function FormHeader($x){ return Form::header($x); }
	public function Form($x){ return Form::open($x); }
	public function FormInput($x){ return Form::input($x); }
	public function FormSubmit($x){ return Form::submit($x); }
	public function FormToken(){ return '<input type="hidden" name="_token" value="'.csrf_token().'"/>'; }
	
	public function FormSelect($x,$y=array())
	{
		    $x = self::FormReturn($x,'select');
			return Form::input($x,$y);
		
	}
	
	public function FormSelect2($url,$arr=array())
	{   
			$default = isset($arr['name']) ? $arr['name'] : null;
			$required = isset($arr['required']) ? $arr['required'] : false;
			$method = isset($arr['method']) ? $arr['method'] : 'POST';
			$observe = isset($arr['observe']) ? $arr['observe'] : null;
			$multiple = isset($arr['multiple']) ? $arr['multiple'] : false;
			$args = isset($arr['args']) ? $arr['args'] : null;
			
			return select2_search($url,$default,$args,$method,$observe,$multiple,$required);
	}
	
	public function FormHidden($x,$y=null)
	{
			return "<input type='hidden' name='{$x}' value='{$y}' />";
	}
	
	public function FormPassword($x)
	{ 
        $x = self::FormReturn($x,'password');
		return Form::input($x);  
	}
	
	public function FormSwitch($name,$value=null,$label=null)
	{ 
		return Form::switch($name,$value,$label);  
	}
	
	public function FormNumber($x)
	{ 
        $x = self::FormReturn($x,'number');
		return Form::input($x);  
	}
	
	public function FormColor($x)
	{ 
        $x = self::FormReturn($x,'color');
		return Form::input($x);  
	}
	
	public function FormFile($x)
	{ 
        $x = self::FormReturn($x,'file');
		return Form::input($x);  
	}
	
	public function FormEmail($x)
	{ 
        $x = self::FormReturn($x,'email');
	    return Form::input($x);  
	}
	
    public function FormRadio($x)
	{ 
        $x = self::FormReturn($x,'radio');
	    return Form::input($x);  
	}

	public function FormCheckbox($x)
	{ 
        $x = self::FormReturn($x,'checkbox');
	    return Form::input($x);  
	}
	
	public function FormDate($x)
	{ 
        $x = self::FormReturn($x,'date');
	    return Form::input($x);  
	}
	
	public function FormText($x)
	{ 
        $x = self::FormReturn($x,'text');
	    return Form::input($x);  
	}

	public function FormMonth($x)
	{ 
        $x = self::FormReturn($x,'month');
	    return Form::input($x);  
	}
	
	public function FormTextArea($x)
	{ 
        $x = self::FormReturn($x,'textarea');
	    return Form::input($x);  
	}
	
	public function FormLabel($x=null)
	{ 
	   return Form::label($x); 
    }

	/*Método para renderizar o arquivo de visualização*/
	final public function render($view, $np_scope = null)
	{
		if(substr($view,0,1) == '@'){
			
			$view = ucfirst(substr($view,1));
			$this->path = 'modules';

		}else{
			$this->path = 'app/Views';
		}

		if (is_string($np_scope)) $np_scope = ['scope' => $np_scope];
		
        $np_scope = !is_null($np_scope) && is_array($np_scope) ? $np_scope : array();
		
		if(array_key_exists('post_title',$np_scope)) $np_scope['page_title'] = $np_scope['post_title'];
		if(array_key_exists('post_description',$np_scope)) $np_scope['page_description'] = $np_scope['post_description'];
		
        if(!array_key_exists('page_title',$np_scope)) $np_scope['page_title'] = NP_NAME;
		if(!array_key_exists('page_description',$np_scope)) $np_scope['page_description'] = NP_DESCRIPTION;
	
		
		
		$this->components = new Json($this->path . '/components.json');

		/*Faz a extração das variaveis do scope*/
		

		extract($np_scope);

		$this->scope = $np_scope;
         
		$view = str_ireplace(['.php', '.view', '.html'], '', $view);

		$view = str_ireplace('.', '/', $view);

		$view_file_vh = $this->local($this->path . '/' . $view . '.vh');
		$view_file = $this->local($this->path . '/' . $view . '.php');
		$view_php = $this->local($this->path . '/' . $view . '.view.php');
		$view_html = $this->local($this->path . '/' . $view . '.view.html');
		$view_html_vh = $this->local($this->path . '/' . $view . '.vh.html');

		$filename = $view_php;

		if (file_exists($view_file)) {
			include($view_file);
		} 
		elseif (file_exists($view_file_vh)) {
			$this->create($view_file_vh, $this->scope,true);
		}elseif (file_exists($view_html_vh)) {
			$this->create($view_html_vh, $this->scope,true);
		}elseif (file_exists($view_html)) {
			$this->create($view_html, $this->scope);
		} elseif (file_exists($view_php)) {
			$this->create($view_php, $this->scope);
		} else {

			$view1 = explode('/', $view);
			$file = $view1[count($view1) - 1];
  
			$msg = $this->text('view.not.found');
			echo '<span style="font-size:15px;color:white;background-color:red;font-family:verdana,arial;padding:2px"><b>view.not.found</b>|<b>' . $this->path . '/' . $file . '</b>|' . $msg . '</span>';
		}
	}

	/*cria um arquivo de visualização*/
	private function create($mfile, $scope = null,$vh=false)
	{
		if (is_array($scope)) extract($scope, EXTR_PREFIX_SAME, "np");

		$view = $mfile;

		$com = $this->local($this->cache . '/' . md5($view) . '.php');

		//gmdate("M d Y H:i:s", mktime(0, 0, 0, 1, 1, 1998));
		$datecom = file_exists($com) ? date('Y.m.d.H.i.s', filemtime($com)) : 'com';
		$dateview = file_exists($view) ? date('Y.m.d.H.i.s', filemtime($view)) : 'view';

		if ($datecom == $dateview) {
			include($com);
		} else {
			$content = file_get_contents($view);

			$content =  $vh ? $this->transformVH($content) : $this->transform($content);

			if (file_put_contents($com, $content))
				include($com);
		}
	}

    private function importx($imports)
	{
		$imports = is_array($imports) ? $imports : array($imports);
		
		foreach($imports as $view)
		{
             $ext = substr($view,-3,3);
             $ext2 = substr($view,-4,4);

             if($ext == '.js' || $ext2 == '.css')
			 {
                if(substr($view,0,1) == '@'){
			
					$view = ucfirst(substr($view,1));
					$this->path = 'modules';
		
				}else{
					$this->path = 'app/Views';
				}

				$view = $this->local($this->path . '/' . $view);

				if(file_exists($view) && $ext == '.js')
				{
                   echo "<script src=\"{$view}\"></script>\n";
				}

				if(file_exists($view) && $ext2 == '.css')
				{
                   echo "<link href=\"{$view}\" rel=\"stylesheet\">\n";
				}

			 }else{
                $this->render($view);
			 }
		}
		
	}


	/*Sintaxe dos arquivos de inclusão*/
	private function dir_tmp($file)
	{
		$painel_header = "/@painel_header\(\)/simU";
		$file = preg_replace($painel_header, "<?php \$this->render('@Painel/Views/header'); ?>", $file);
		$painel_footer = "/@painel_footer\(\)/simU";
		$file = preg_replace($painel_footer, "<?php \$this->render('@Painel/Views/footer'); ?>", $file);
		
		/*Include de templates*/
		$import = "/@import\(({$this->all})\)/simU";
		$file = preg_replace($import, "<?php \$this->importx($1); ?>", $file);
		
		
		$include = "/@include\(({$this->all})\)/simU";
		$file = preg_replace($include, "<?php \$this->render($1,\$this->scope); ?>", $file);
		
		///////////////////////////////
		$componet2 = "/@component\(({$this->all})\)[^{][^\);]\s/simU";
		$file = preg_replace($componet2, "<?php echo \$this->component($1); ?>", $file);	

		/*Função para token contra ataque crsf*/
		$crsf = "/\{\{ ?csrf_field\(\) ?\}\}/simU";
		$file = preg_replace($crsf, '<input type="hidden" name="_token" value="{{csrf_token()}}"/>', $file);

		$event = "/\{\{ ?event_field\(({$this->all})\) ?\}\}/simU";
		$file = preg_replace($event, '<input type="hidden" name="_event" value=$1/>', $file);

		return $file;
	}
  
/******* Renderiza um componente*******/
	private function component($component,$scope=null)
	{
		  $component = 'np_'.str_ireplace('-','_',$component);
		  if(function_exists($component))
		  {
			return call_user_func($component,$scope);
		  }
	}
	

	/*Sintaxe para condicionais*/
	private function dir_if($file)
	{
		$file = str_ireplace('@endtemplate','} ?>',$file);
		
		$componet23 = "/@template\(({$this->all})\)/simU";
		$file = preg_replace($componet23, '<?php function np_$1($scope=null){ if(is_array($scope)) extract($scope);', $file);

        ///////////////////////////////
    
		/*If e Elseif*/
		$if = "/@if\(({$this->all})\)/simU";
		$file = preg_replace($if, "<?php if($1): ?>", $file);

		$elseif = "/@elseif\(({$this->all})\)/simU";
		$file = preg_replace($elseif, "<?php elseif($2): ?>", $file);

		/*Else e fechamento do if*/
		$file = str_ireplace("@else", "<?php else: ?>", $file);
		$file = str_ireplace("@endif", "<?php endif; ?>", $file);

		return $file;
	}

    private function verbatim($html)
	{
		$verbatim = "/\@verbatim ({$this->all}) @endverbatim /imU";
		$html = preg_replace($verbatim, "$1", $html);
		
		$echox = "/\{{2}{$this->all}\}{2}/imU";
		$html = preg_replace($echox, "(___$1___)", $html);
		return $html;
	}

	/*Sintaxe para imprimir*/
	private function dir_echo($file)
	{
		$echo = "/\{{2}{$this->all}\}{2}/imU";
		$echoFilter = "/\{{2}{$this->all}\|{$this->all}\}{2}/imU";
		$echoHTML = "/\{\!{$this->all}\!\}/imU";
		$echoCall = "/\{\?{$this->all}\?\}/imU";
		
		$echoxx = "/@@{$this->all}/imU";
		$file = preg_replace($echoxx, "_(_x_x_x_)_$1", $file);
		
		$echox = "/@\{{2}{$this->all}\}{2}/imU";
		$file = preg_replace($echox, "(___$1___)", $file);
 
		$file = preg_replace($echo, "<?php echo htmlspecialchars(trim($1), ENT_QUOTES); ?>", $file);
        
		$file = preg_replace($echoFilter, "<?php echo htmlspecialchars($2(trim($1)), ENT_QUOTES); ?>", $file);

		$file = preg_replace($echoHTML, "<?php echo html_entity_decode(trim($1)); ?>", $file);

		$file = preg_replace($echoCall, "<?php $1; ?>", $file);
		
		$file = str_ireplace(['{!{', '@php', '@endphp'], ['{{', '<?php', '?>'], $file);
		$file = str_ireplace('(___','{{',$file);
		$file = str_ireplace('___)','}}',$file);
		$file = str_ireplace('_(_x_x_x_)_','@',$file);

		return $file;
	}

	/*sintaxe para o loop*/
	private function dir_for($file)
	{

		$in = "/@in\(({$this->all})\)/simU";
		$for = "/@for\(({$this->all})\)/simU";
		$foreach = "/@foreach\(({$this->all})\)/simU";

		$file = preg_replace($in, "<?php foreach($1 as \$items): extract(\$items); ?>", $file);
		$file = preg_replace($for, "<?php for($1): ?>", $file);
		$file = preg_replace($foreach, "<?php foreach($1): ?>", $file);
		
		$file = str_ireplace('@endforeach','<?php endforeach; ?>', $file);
		$file = str_ireplace('@endin','<?php endforeach; ?>', $file);
		$file = str_ireplace('@endfor','<?php endfor; ?>', $file);
		
		
		$x = "/<<{$this->all}>>/simU";
		$file = preg_replace($x, "<?php $1; ?>", $file);
		
		$formx = "/{{form\.({$this->all})}}/simU";
		$file = preg_replace($formx, '{!form::$1!}', $file);
		
		$form = "/{{form::({$this->all})}}/simU";
		$file = preg_replace($form, '{!form::$1!}', $file);
		
		$grid1 = "/@grid\(\"({$this->all})\"\)/simU";
		$file = preg_replace($grid1, '@grid($1)', $file);
		
		$grid12 = "/@grid\(\'({$this->all})\'\)/simU";
		$file = preg_replace($grid12, '@grid($1)', $file);
	
		
		$grid = "/@grid\(({$this->all})\)/simU";
		$file = preg_replace($grid, '<div class="$1">', $file);
		
		$file = str_ireplace('@endgrid','</div>', $file);
		
		$file = str_ireplace('form::open','$this->Form', $file);
		$file = str_ireplace('form::input','$this->FormInput', $file);
		$file = str_ireplace('form::select','$this->FormSelect', $file);
		$file = str_ireplace('form::submit','$this->FormSubmit', $file);
		$file = str_ireplace('form::label','$this->FormLabel', $file);
		
		$file = str_ireplace('form::close','$this->FormClose', $file);
		
		$file = str_ireplace('form::password','$this->FormPassword', $file);
		$file = str_ireplace('form::number','$this->FormNumber', $file);
		$file = str_ireplace('form::checkbox','$this->FormCheckbox', $file);
		$file = str_ireplace('form::radio','$this->FormRadio', $file);
		$file = str_ireplace('form::email','$this->FormEmail', $file);
		$file = str_ireplace('form::text','$this->FormText', $file);
		$file = str_ireplace('form::date','$this->FormDate', $file);
		$file = str_ireplace('form::file','$this->FormFile', $file);
		$file = str_ireplace('form::color','$this->FormColor', $file);
		$file = str_ireplace('form::month','$this->FormMonth', $file);
		$file = str_ireplace('form::textarea','$this->FormTextArea', $file);
		$file = str_ireplace('form::longText','$this->FormTextArea', $file);
		$file = str_ireplace('form::hidden','$this->FormHidden', $file);
		$file = str_ireplace('form::header','$this->FormHeader', $file);
		$file = str_ireplace('form::select2','$this->FormSelect2', $file);
		$file = str_ireplace('form::token','$this->FormToken', $file);
		$file = str_ireplace('form::switch','$this->FormSwitch', $file);

		return $file;
	}

	/*Tradução do arquivo*/
	private function dir_lang($file)
	{
		
		$var = "/@var\(({$this->all})\)/simU";
		$file = preg_replace($var, "<?php $$1; ?>", $file);
		
		
		
		$var = "/@\(({$this->all})\)\+\+/simU";
		$file = preg_replace($var, "<?php $$1++; ?>", $file);
		

		$lang = "/@use\(({$this->all})\)/simU";
		$file = preg_replace($lang, "<?php \$this->merge($1); ?>", $file);

		$lang = "/@usei\(({$this->all})\)/simU";
		$file = preg_replace($lang, "<?php \$this->usei($1); ?>", $file);
		
		$langM = "/@module\(({$this->all})\)/simU";
		$file = preg_replace($langM, "<?php \$this->module($1); ?>", $file);

		$text = "/#([-A-Za-zÀ-ú0-9\.\-\_]+)#/simU";
		$file = preg_replace($text, "<?php echo \$this->text('$1'); ?>", $file);

		$view = "/@view\(({$this->all})\)/simU";
		

		$file = preg_replace($view, "<?php \$this->loadComponent($1); ?>", $file);

		
		
		
		

		return $file;
	}

	/*Carrega um componente*/
	public function loadComponent($name, $scope = null)
	{
		$component = $this->components->get($name);
		if ($component) {
			$this->render($component, $scope);
		}
	}


	/*Transforma tudo em algo legivel para o PHP*/
	public function transform($content)
	{
		$content = $this->dir_lang($content);
		$content = $this->dir_tmp($content);
		$content = $this->dir_for($content);
		$content = $this->dir_echo($content);
		$content = $this->dir_if($content);
		$content = preg_replace("/: \?>\)/simU", "): ?>", $content);
		return $content;
	}
    

	private function echoVH($file)
	{
		$echo = "/\{{2}{$this->all}\}{2}/imU";
		
		$echox = "/\{{1}\!{$this->all}\!\}{1}/imU";

		$echoFilter = "/\{{2}{$this->all}\|{$this->all}\}{2}/imU";
		
		$file = preg_replace($echoFilter, "<?php echo htmlspecialchars($2(trim($1)), ENT_QUOTES); ?>", $file);

		$file = preg_replace($echoFilter, "<?php echo htmlspecialchars($2(trim($1)), ENT_QUOTES); ?>", $file);

        $file = preg_replace($echox, "<?php echo $1; ?>", $file);

		$file = preg_replace($echo, "<?php echo htmlspecialchars(trim($1), ENT_QUOTES); ?>", $file);

		return $file;
	}

	private function phpVH($file)
	{
         $cdnjs = "/{cdn\.js ({$this->all})}/simU";
		 $file = preg_replace($cdnjs, "<?php echo \$this->importCDN('js',$1); ?>", $file);
		 
		 $cdncss = "/{cdn\.css ({$this->all})}/simU";
		 $file = preg_replace($cdncss, "<?php echo \$this->importCDN('css',$1); ?>", $file);

		/*Include de templates*/
		$import = "/{import ({$this->all})}/simU";
		$file = preg_replace($import, "<?php \$this->importx($1); ?>", $file);

        $include = "/{include ({$this->all})}/simU";
		$file = preg_replace($include, "<?php \$this->render($1,\$this->scope); ?>", $file);

		$file = str_ireplace('{php}', "<?php ", $file);
		$file = str_ireplace('{/php}', " ?>", $file);
		return $file;
	}
     /*Inicio do VH*/
      private function ifVH($file)
	  {
        /*If, ElseIf, Else e Switch*/
		$if = "/{if ({$this->all})}/simU";
		$case = "/{case ({$this->all})}/simU";
        $elseif = "/{elseif ({$this->all})}/simU";
		$switch = "/{switch ({$this->all})}/simU";

		$file = preg_replace($if, "<?php if($1): ?>", $file);
		$file = preg_replace($case, "<?php case $1: ?>", $file);
		$file = preg_replace($elseif, "<?php elseif($1): ?>", $file);
	    $file = preg_replace($switch, "<?php switch($1): ?>", $file);

		$file = str_ireplace('{/case}', "<?php break; ?>", $file);
		$file = str_ireplace('{else}', "<?php else: ?>", $file);
	    $file = str_ireplace('{/if}', "<?php endif; ?>", $file);
		$file = str_ireplace('{/switch}', "<?php endswitch; ?>", $file);
		$file = str_ireplace('{default}', "<?php default: ?>", $file);

		return $file;
	  }

      
       
	  private function forVH($file)
	  {  
        /*If, ElseIf, Else e Switch*/
		$for = "/{for ({$this->all})}/simU";
		$foreach = "/{foreach ({$this->all})}/simU";
		$while = "/{while ({$this->all})}/simU";
		$in = "/{in ({$this->all})}/simU";

         $file = str_ireplace("{csrf_field}", '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>', $file);
		 $file = str_ireplace("{csrf_token}", '<?php echo csrf_token(); ?>', $file);


		$file = preg_replace($in, "<?php foreach($1 as \$np_items_in): extract(\$np_items_in); ?>", $file);

		$file = preg_replace($for, "<?php for($1): ?>", $file);
		$file = preg_replace($while, "<?php while($1): ?>", $file);
		$file = preg_replace($foreach, "<?php for($1): ?>", $file);
		$file = str_ireplace('{/for}', "<?php endfor; ?>", $file);
		$file = str_ireplace('{/foreach}', "<?php endforeach; ?>", $file);
		$file = str_ireplace('{/in}', "<?php endforeach; ?>", $file);
		$file = str_ireplace('{/while}', "<?php endwhile; ?>", $file);

		return $file;
	  }
	  
	 private function templateVH($file)
	 {
		$file = str_ireplace('{/template}','} ?>',$file);
		
		$for = "/{template ({$this->all})}/simU";
		
		$file = preg_replace($for, '<?php function np_$1($scope=null){ if(is_array($scope)) extract($scope);', $file);
		
		
		$forx = "/{component ({$this->all})}/simU";
		$file = preg_replace($forx, "<?php echo \$this->component($1); ?>", $file);	
		
		return $file;
	 }
     
	 public function importCDN($path,$files)
	 {
		 $files = is_string($files) ? array($files) : $files;
		 $cdns = null;
		 foreach($files as $file)
		 {
			$file = str_ireplace('.js','',$file);
			$file = str_ireplace('.css','',$file);
			
			if($path == 'js')
			{
				$file = asset("cdn/js/{$file}.js");
				$cdns .= "<script src=\"{$file}\"></script>\n";
			}elseif($path == 'css')
			{
				$file = asset("cdn/css/{$file}.css"); 
				$cdns .= "<link href=\"{$file}\" rel=\"stylesheet\">\n";
			}else{
				
			}
		 }
		 return $cdns;
	 }
	 
	/*Transforma tudo em algo legivel para o PHP*/
	public function transformVH($content)
	{		
		$content = $this->templateVH($content);
		
		$content = str_ireplace('!{', '___x__xx__xx', $content);
		
		
		$content = $this->phpVH($content);
		$content = $this->forVH($content);
		$content = $this->echoVH($content);
		
		$content = $this->ifVH($content);
		
		$content = str_ireplace('___x__xx__xx', '{', $content);

		
		return $content;
	}
}
