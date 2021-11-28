<?php 
namespace Modules\FileManager\Controllers; 

use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;

class FileManagerController extends Controller
{
   
     public function showCode()
	 {
		 $request = new Request;
		 $file = $request->get('file');
		 
		 $file = file_get_contents($file,true);
		 
		 return $file;
	 }
   
    //Verifica o tipo de arquivo
    private function renderDir($dir,$path='uploads')
    {
       $ext = array(
         'jpge',
         '.jpg',
         '.png',
         '.gif',
         '.pdf',
         'json',
         'html',
         '.php'
       ); 

       $codes = array(
        'json',
        'html',
        '.php',
		'.js'
      );
      
       $dir_ext = substr($dir,-4);
       
       if(in_array($dir_ext,$ext))
       {
		 $dir_path = $path.'/'.$dir;
         $dir = url($path.'/'.$dir);
         
         $name = explode('/',$dir);
         $name = $name[count($name)-1];

         if(in_array($dir_ext,$codes)){
            return "<li class='list-group-item'>
            <a href='#' id='{$dir_path}' data-bs-toggle='modal' class='btn-edit-file' data-bs-target='#modalEditFile'><i class='material-icons' style='font-size:50px'>code</i></a>
              <span style='position:relative;top:-20px'>{$name}<br></span>
            </li>";
         }else{
			 
            return "<li class='list-group-item'>
                       <img style='width:50px;height:50px' src='{$dir}'>
                       <span>{$name}</span>
					</li>"; 
         }

       }else{
          $url = url("filemanager/dir?dir={$path}/{$dir}"); 
          return "<li class='list-group-item'>
                     <a href='$url'><i class='material-icons' style='font-size:50px'>folder</i></a>
                     <span style='position:relative;top:-20px'>{$dir}</span>
                  </li>";
       }
    }

    public function showDir()
    {
          $dir = new Request;
          $path = $dir->get('dir');
          $html = $this->enterDir($path);
        
          $data = array(
            'html'=>$html,
            'path'=>$path
        );

	    return view("@FileManager/Views/index", $data);
    }

    /*Lista o diretório*/
    public function enterDir($path)
    {
        $dirs = scandir($path);
        $html = null;
        foreach($dirs as $dir)
        {
          if($dir != '.' && $dir != '..'){
            $html .= $this->renderDir($dir,$path);
          }
          
        }
        return $html;
    }

    //Retorna a view de navegação
	public function index()
	{  

        $request = new Request;
        $path =  $request->get('init','uploads');

        $path = ($path == 'uploads' OR   $path == '../storage') ? $path : 'uploads'; 

        $dirs = scandir($path);
        $html = null;

        foreach($dirs as $dir)
        {
           if($dir != '.' && $dir != '..')
           {
              $html .=  $this->renderDir($dir,$path);
           }
        }

        $data = array(
            'html'=>$html,
            'path'=>'uploads'
        );

	    return view("@FileManager/Views/index", $data);
	} 

    //Retorna a view de importação de imagem
    public function importImage()
    {
        return view("@FileManager/Views/import");
    }

} 
