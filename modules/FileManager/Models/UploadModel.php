<?php
namespace Modules\FileManager\Models; 

use Nopadi\MVC\Model;

    class UploadModel extends Model
    {
	  protected $table = "uploads";
	  
	  public static function model()
	  {
		return new UploadModel();
	  }
     /*Registra a instancia do arquivo no banco de dados. */
     public function insertUpload($path,$description=null)
	 {
        $ext = substr($path,-4,4); 
		$name = explode('/',$path);
		$name = $name[count($name) - 1];
		
		$imgs = array('.png','.gif','.jpeg','.jpg');
		$pdfs = array('.pdf');
        $docs = array('.pdf','docx','.xls','.zip','.rar');
		$codes = array('html','.js','.css','.php');

        if(in_array($ext,$imgs)) $type = 'img';
		elseif(in_array($ext,$docs)) $type = 'doc';
		elseif(in_array($ext,$pdfs)) $type = 'pdf';
		elseif(in_array($ext,$codes)) $type = 'code';
		else $type = 'any';
        //any para arquivos não identificados

		$data = array(
			'path'=>$path,
			'name'=>$name,
			'type'=>$type,
			'description'=>$description);

		$this->insert($data);

	  }
    }
