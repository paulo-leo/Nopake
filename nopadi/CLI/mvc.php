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
     echo "Diretório {$name} criado com sucesso.";
     
	 mkdir($path_mod."/Controllers", 0777,true); 
	 mkdir($path_mod."/Models", 0777,true); 
	 mkdir($path_mod."/Views", 0777,true); 
	 
   }
	
	
}

?>