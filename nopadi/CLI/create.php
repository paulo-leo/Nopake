<?php

create($arg1,$arg2,$arg3);

function create($a1,$a2,$a3){	
	switch($a1){
		case 'controller' : 
		create_controller($a2,$a3);
		break;
		/*Middleware*/
		case 'middleware' : 
		create_middleware($a2,$a3);
		break;
		/*schema*/
		case 'schema' : 
		create_schema($a2);
		break;
		/*Model*/
		case 'model' : 
		create_model($a2,$a3);
		break;
		/*View*/
		case 'view' : 
		create_view($a2,$a3);
		break;
		default : echo 'Erro ao execultar comando create.';
	}
}
/*Cria um schema*/
function create_schema($name){
		if(!is_null($name)){
			
			    $dir = "app/Models/Schemas";
				
			    if(!is_dir($dir)) mkdir($dir,0755,true);
				 
			     $path = $dir.'/'.$name.'.php';
				
				if(file_exists($path))
					echo 'Erro ao criar schema';
				else{
					 $content = "<?php \nnamespace App\Models\Schemas; \n\nuse Nopadi\Base\CreateSchema;\n\nclass ".$name." extends CreateSchema\n{\n \n   public function create()\n   {\n  \n   } \n \n   public function modify()\n   {\n  \n   }\n \n}\n";
					 
					  $file = fopen($path,'w');
					  
					  if(fwrite($file,$content)){
						  fclose($file);
						  echo 'Schema criada com sucesso em: '.str_ireplace('\\','/',$path);
					  }else echo 'Erro ao criar arquivo.';
				}
	
		}else echo 'Informe o nome do schema.';
}
/*Cria um controller*/
function create_controller($name,$dir){
		if(!is_null($name)){
			
			    $dir = is_null($dir) ? 'app/Controllers' : $dir;
				
			    if(!is_dir($dir)) mkdir($dir,0755,true);
				 
			    $path = $dir.'/'.$name.'.php';
				
				if(file_exists($path))
					echo 'Erro ao criar controlador';
				else{
					 $content = "<?php \nnamespace App\Controllers; \n\nuse Nopadi\MVC\Controller;\n\nclass {$name} extends Controller\n{\n \n   public function index()\n   {\n  \n   }\n} \n";
					 
					  $file = fopen($path,'w');
					  
					  if(fwrite($file,$content)){
						  fclose($file);
						  echo 'Controlador criado com sucesso em: '.str_ireplace('\\','/',$path);
					  }else echo 'Erro ao criar arquivo.';
				}
	
		}else echo 'Informe o nome do controlador.';
}
/*Cria um model*/
function create_model($name,$dir){
		if(!is_null($name)){
			
			    $dir = is_null($dir) ? 'app/Models' : $dir;
				
			    if(!is_dir($dir)) mkdir($dir,0755,true);
				 
			    $path = $dir.'/'.$name.'.php';
				
				if(file_exists($path))
					echo 'Erro ao criar controlador';
				else{
					 $content = "<?php \nnamespace App\Models; \n\nuse Nopadi\MVC\Model;\n\nclass {$name} extends Model\n{\n \n} \n";
					 
					  $file = fopen($path,'w');
					  
					  if(fwrite($file,$content)){
						  fclose($file);
						  echo 'Modelo criado com sucesso em: '.str_ireplace('\\','/',$path);
					  }else echo 'Erro ao criar arquivo.';
				}
	
		}else echo 'Informe o nome do Modelo.';
}
/*Cria um middleware*/
function create_middleware($name,$dir){
		if(!is_null($name)){
			
			    $dir = is_null($dir) ? 'app/Middlewares' : $dir;
				
			    if(!is_dir($dir)) mkdir($dir,0755,true);
				 
			    $path = $dir.'/'.$name.'.php';
			
				
				if(file_exists($path))
					echo 'Erro ao criar Middleware';
				else{
					 $content = "<?php \nnamespace App\Middlewares; \n\nuse Nopadi\Http\Middleware;\n\nclass {$name} extends Middleware\n{\n \n   public function handle(\$arg)\n   {\n  \n   }\n} \n";
					 
					  $file = fopen($path,'w');
					  
					  if(fwrite($file,$content)){
						  fclose($file);
						  echo 'Middleware criado com sucesso em: '.str_ireplace('\\','/',$path);
					  }else echo 'Erro ao criar arquivo.';
				}
	
		}else echo 'Informe o nome do Middleware.';
}
/*Cria uma view*/
function create_view($name,$dir){
		if(!is_null($name)){
				
				$dir = is_null($dir) ? 'app/Views' : 'app/Views/'.$dir;
				
			    if(!is_dir($dir)) mkdir($dir,0755,true);
				 
			    $path = $dir.'/'.$name.'.view.html';
				
				if(file_exists($path))
					echo 'Erro ao criar Visualizador';
				else{
$content = '
<!DOCTYPE html>
<html lang="{{NP_LANG}}">
<head>
    <title>'.$name.'</title>
<meta charset="{{NP_CHARSET}}">
   {!style()!}
</head>
<!--Body of your app-->
<body>
<div class="container">
<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-4">'.$name.'</h1>
    <p class="lead">#welcome#</p>
</div>
   </div>
     </div>
</body>
</html>';
					 
					  $file = fopen($path,'w');
					  
					  if(fwrite($file,trim($content))){
						  fclose($file);
						  echo 'Visualizador criado com sucesso em: '.str_ireplace('\\','/',$path);
					  }else echo 'Erro ao criar arquivo.';
				}
	
		}else echo 'Informe o nome do Visualizador(View).';
}
?>
