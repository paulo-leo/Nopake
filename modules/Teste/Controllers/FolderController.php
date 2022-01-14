<?php 
namespace Modules\FileManager\Controllers; 

use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;

class FolderController extends Controller
{
   //Função para criação de diretorio
   public function createDir()
   {
	   	$request = new Request;
		$path = $request->getString('path');
		$dir_name = $request->getString("dirName");
	
		$dir_name = $this->filenameSafe($dir_name);

	   	mkdir($path.$dir_name);
   }

   //Função para deletar diretorio
   public function removeDir()
   {
		$request = new Request;
		$path = $request->getString('path');
		$dir_name = $request->getString("dirName");
 
		rmdir($path.$dir_name);
   }

   //Função para abertura de arquivos
   public function openFolder()
   {
        $request = new Request;        
        $path = $request->getString('path');
        $dir = scandir($path);

        /* 
            A chave é o nome da pasta
            O valor é o tipo do sendo ele arquivo, pasta ou indefinido 
            -> informação para aribuição de eventos no front-end
        */

        for($i=0; $i < sizeof($dir); $i++)
        {
            if(is_dir($path."/".$dir[$i]))
            {
                $json[$dir[$i]] = "dir folder";
            }
            else if(is_file($path."/".$dir[$i]))
            {
                $extension = substr($dir[$i], -4, 4);

                switch($extension){
                    case ".png";
                    case ".jpg";
                    case ".gif";
                    case "jpeg";
                        $json[$dir[$i]] = "file image";
                    break;
                    
                    case ".gif";
                        $json[$dir[$i]] = "file gif";
                    break;

                    case ".pdf";
                        $json[$dir[$i]] = "file picture_as_pdf";
                    break;

                    case ".txt";
                        $json[$dir[$i]] = "file text_snippet";
                    break;                    
                }

            }
            else
            {
                $json[$dir[$i]] = "undefined";
            }
        }
        
       return json($json);
   }

   //Função que remove caracteres especiais
   public function filenameSafe($name) 
   {
    	$except = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
    	return str_replace($except, '', $name);
   }

} 





