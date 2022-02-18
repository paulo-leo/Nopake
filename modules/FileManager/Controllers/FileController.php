<?php 
namespace Modules\FileManager\Controllers;

use Nopadi\Base\DB;
use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Nopadi\FS\Upload;
use Nopadi\FS\UploadImage;

use Modules\FileManager\Models\UploadModel;

class FileController extends Controller
{
	private $path;
	/*Faz o upload e inserção do arquivo na base de dados*/
	public function storeFile($r=false)
	{
		$request = new Request;
		
		$public = $request->getBit('public');
		$name_random = $request->getBool('name_random');
		$file_write = $request->getBool('file_write');
		$file_version = $request->getBool('file_version');
		$description = $request->get('description');
		
		if($r)
		{
			$public = 0;
		    $name_random = 1;
		}
		
		$uploads = new Upload;
		
		$public = $public == 1 ? 'uploads' : '../storage/uploads';
		
	
		 $public =  $public.'/'.date('Y/m');
		 $public = str_ireplace('//','/',$public);
		 if(!file_exists($public))
		 {
			  mkdir($public, 0777,true);
		 }
		
		$uploads->versionFile($file_version);
        $uploads->randomFile($file_write);
		$uploads->setUploadDir($public);
		$uploads->setRandomName($name_random);
		
		
		$uploads->setAllowedExtensions(['png','pdf','jpg','jpeg','gif','doc','docx','csv','xls','text','txt','xlsx']);
		
		$file = $request->getFile('userfile');

		
		$file = $uploads->uploadFile($file);
	
	     if($file)
		 {
			 $path =  $uploads->getFilePath();
			 $description = strlen($description) < 3 ?  $uploads->getFileName() : $description;
			 
			 $this->path = $path;
			 
			 $model = new UploadModel;
			 $model = $model->insertUpload($path,$description);
			 
			 if($model) return alert($uploads->getErrorMessage(),'success');
			 else return alert('Erro salvar arquivo no banco de dados','danger');
			 
		 }else{
			 return alert($uploads->getErrorMessage(),'error');
		 }
	}
	
	public function storeFileOne()
	{
		
		return alert('Chegou!');
		
	}
	
	/*Retorna o endereço*/
	public function getPath()
	{
		return $this->path;
	}
	
	public function storeImage()
    {
        $request = new Request();
        $quality_opt = $request->getInt('image-quality');
        $description = $request->get('description');
        
        switch($quality_opt)
        {
            case 1:
                $resolution_x = 1920;
                $resolution_y = 1080;
            break;
            case 2:
                $resolution_x = 1280;
                $resolution_y = 720;            
            break;
            case 3:
                $resolution_x = 960;
                $resolution_y = 540; 
            break;
            case 4:
                $resolution_x = 640;
                $resolution_y = 360; 
            break;
			
			default :
                $resolution_x = 640;
                $resolution_y = 360;
        }        


		 $public = 'uploads/'.date('Y/m');
		 $public = str_ireplace('//','/',$public);
		 if(!file_exists($public))
		 {
			  mkdir($public, 0777,true);
		 }

        $upload_image = new UploadImage(array(
            'height'=>$resolution_x,
            'width'=>$resolution_y,
            'name'=>'userfile',
            'folder'=>$public
        ));

        if($request->checkError()){ 
            $path = $upload_image->save();
            if($path){

                 $name = explode('/',$path);
                 $name = $name[count($name) - 1];

                 UploadModel::model()->insertUpload($path,$description); 
                $msg = "Arquivo: {$name} carregado com sucesso no servidor.";  
                return alert($msg,'success');
            }else{
                $msg = 'Upload finalizado com sucesso';  
                return alert($msg,'error');
            }
        }else{
            return alert($request->getErrorMessage(),'danger');	
        }            
    }
} 