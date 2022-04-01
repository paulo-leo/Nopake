<?php

if(substr($arg1,0,1) == '@')
{
   
   $arg1 = substr($arg1,1);

   $arg1 = explode('/',$arg1);
   $name = ucfirst($arg1[0]);

   $namespace_c = "Modules\{$name}\Controllers";
   $namespace_m = "Modules\{$name}\Models";
   
   $path_mod = "modules/{$name}";
   if(!is_dir($path_mod))
   {
	  mkdir($path_mod, 0777,true); 
     echo "Diretório: {$name} criado com sucesso.\n";
   }
	
   if(!is_dir($path_mod."/Controllers"))
   {
      mkdir($path_mod."/Controllers", 0777,true); 
      echo "Diretório: Controllers criado com sucesso.\n";
   }

   if(!is_dir($path_mod."/Models"))
   {
      mkdir($path_mod."/Models", 0777,true); 
      echo "Diretório: Models criado com sucesso.\n";
   }

   if(!is_dir($path_mod."/Views"))
   {
      mkdir($path_mod."/Views", 0777,true); 
      echo "Diretório: Views criado com sucesso.\n";
   }

   if(!file_exists($path_mod."/{$name}.php"))
   {
      $content = "<?php \n";
      $content .= "namespace Modules\\$name;\n\n";
      $content .= " use Nopadi\MVC\Module;\n";
      $content .= " use Nopadi\Http\Route;\n";
      $content .= " use Nopadi\Base\Table;\n\n";
      $content .= "class $name extends Module\n{\n\n";
      $content .= "  /*Método principal do módulo $name*/\n";
      $content .= "  public function main(){ /*Escreva as suas rotas aqui...*/ }\n\n";
      $content .= "  /*Esse método será executado automaticamente ao ativar o módulo */\n";
      $content .= "  public function on(){ } \n\n";
      $content .= "  /*Esse método será executado automaticamente ao desativar o módulo */\n";
      $content .= "  public function off(){ }\n";
      $content .= "} \n";

      file_put_contents($path_mod."/{$name}.php", $content);
      $content = null;
      echo "Arquivo de módulo: {$name}.php criado com sucesso \n";
   }

   if(!file_exists($path_mod."/config.json"))
   {
	   $config = '{
         "name": "Nome do módulo",
         "description": "Descrição do módulo",
         "version": "0.1",
         "author": "Nome do autor/dev do módulo",
         "license": "MIT",
         "require": ["php7"],
         "url": "url do módulo",
         "icon": "link",
         "color":"info",
         "route":"dashboard/xxxxxxxx"
         }';
         file_put_contents($path_mod."/config.json", $config);
         echo "Arquivo: config.json de configuração automatica de módulo criado com sucesso. \n";
   }

   if(isset($arg2))
   {
      $arg2 = explode(',',$arg2);

      if($arg2[0] != 'null')
      {
        $model = explode(':',$arg2[0]);
        $table = isset($model[1]) ? $model[1] : false;
        $model = $model[0];
        $model = ucfirst($model);

        if(!file_exists($path_mod."/Models/".$model.".php"))
        {
         $contentm = "<?php \n";
         $contentm .= "namespace Modules\\$name\Models;\n\n";
         $contentm .= " use Nopadi\MVC\Model;\n\n";

         $contentm .= "class $model extends Model\n{\n\n";
      
         if($table)
         {
            $contentm .= "  protected \$table = '$table';\n";
         } 
         
         $contentm .= "\n}";

         file_put_contents($path_mod."/Models/".$model.".php", $contentm);
         echo "Arquivo de modelo do MVC criado com sucesso. \n";
    
        }
      }

      if(isset($arg2[1]))
      {

        $controller = $arg2[1];
        $controller = ucfirst($controller);

        if(!file_exists($path_mod."/Controllers/".$controller.".php"))
        {
         $contentm = "<?php \n";
         $contentm .= "namespace Modules\\$name\Controllers;\n\n";
         $contentm .= " use Nopadi\MVC\Controller;\n\n";

         $contentm .= "class $controller extends Controller\n{\n\n";
      
         $contentm .= "   public function index(){ }\n";
         
         $contentm .= "\n}";

         file_put_contents($path_mod."/Controllers/".$model.".php", $contentm);
         echo "Arquivo de controle do MVC criado com sucesso. \n";
    
        }
      }
   }
}

?>