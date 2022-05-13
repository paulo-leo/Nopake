<?php 
namespace Modules\FileManager\Controllers;

use Nopadi\Base\DB;
use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Modules\FileManager\Models\UploadModel;

class FileIncludeController extends Controller
{  
	/*Lista as imagens que es*/
    public function getUploads()
	{  
		$request = new Request;
		$id = $request->get('id');
				
		$file = false;
		
		if(is_numeric($id)){
			$file = new UploadModel;
			$file = $file->where('id',$id)->get('o');
		}
		
		if($file){
		   $file = [
		    'code'=>'201', 
		    'path'=>asset($file->path),
			'description'=>$file->description
		   ];
		}else{
			$file = array('code'=>'401');
		}
		json($file);
	}
	
	/*Pega o caminho de um arquivo especifico via ID*/
    public function getFileUpload()
	{  
		$request = new Request;
		$id = $request->get('id');
		
		$msg_error = "<div style='border-radius:5px;border:1px solid #FF7373; text-align:center;font-family:arial;color:#FF7373'><h1>404</h1><p>Nenhum arquivo foi vinculado ou localizado para este registro.</p></div>";

		if(is_numeric($id)){
			$file = new UploadModel;
			$file = $file->where('id',$id)->get('o');
			if($file){
				
				$file = $file->path;
				$file = str_ireplace('../storage/','storage/',$file);
				
				 $uri = new URI();
	             $file = $uri->local($file);

                 $this->headerFile($file);
				 return file_get_contents($file);

			}else{
				return $msg_error;
			}
		}else{
			return $msg_error;
		}
		
	}

	/*Leitura de arquivo privado*/
    private function headerFile($file)
	{
		$ext = strtolower(substr($file,-4,4));

        switch($ext)
		{
            case '.pdf' : header("Content-type:application/pdf"); break;
			case '.png' : header("Content-type:image/png"); break;
			case '.jpg' : header("Content-type:image/jpg"); break;
			case 'jpeg' : header("Content-type:image/jpeg"); break;
			case '.gif' : header("Content-type:image/gif"); break;
			case '.xls' :header("Content-Type: application/vnd.ms-excel; charset=utf-8"); break;
		}
	}

	
    public function getFiles()
    {  
	  $files = new UploadModel;
	  
	  $request = new Request;
	  $search = $request->get('search');
	  $type = $request->get('type');
	  
	  $files = $files->select(['*']);
	  
	  if(strlen($search) >= 2)
	  {
		$files = $files->where('description','like',$search);
	  }
	  
	  if(false){
		  $files = $files->where('path','like.','uploads');
	  }
	  
	  if($type)
	  {
		$files = $files->where('type',$type);
	  }
	  
	  //var_dump($files);
	  
	  $files = $files->orderBy('id desc')->paginate(10);
	  
	  $results = $files->results;
	  $next = $files->next;
	  $previous = $files->previous;
	  
	  
	  if($results){
	  
      $results = np_map($results,'path',function($path){
		  
		 $type = substr($path,-4,4);
		 
		 if($type == '.pdf')
		 {
			return asset('uploads/img/pdf.png'); 
		 }
		 elseif($type == '.doc' || $type == 'docx')
		 {
			return asset('uploads/img/doc.png'); 
		 }
		 else{
			 return asset($path); 
		 } 
	   });
	  }
	  
	  json(array(
	    'results'=>$results,
		'previous'=>$previous,
		'next'=>$next
	  ));
	  
    }
	
	public function getAttachments()
    {  
	   $request = new Request;
	   $table_name = $request->get('table_name');
	   $table_id = $request->get('table_id');
	  
	  $files = DB::table('attachments');
	  
	  $files = $files->as('f')
	  ->select(['u.path as path','u.description'])
	  ->where([
	   ['table_name',$table_name],
	   ['table_id',$table_id]
	  ])->leftJoin('uploads u','u.id','f.file_id')
	  ->orderBy('f.id desc')
	  ->get();

     
	  $results = np_map($files,'path',function($path){
		  
		 $type = substr($path,-4,4);
		 
		 if($type == '.pdf')
		 {
			return asset('uploads/img/pdf.png'); 
		 }elseif($type == '.doc' || $type == 'docx')
		 {
			return asset('uploads/img/doc.png'); 
		 }
		 else{
			 return asset($path); 
		 }
	  });
	 
	  json($results);
	 
    }

	/*Faz anexo do arquivo caso ele não exista no registro*/
	public function addAttachment()
	{
       $request = new Request;
	   
	   $file_id = $request->get('file_id');
	   $table_name = $request->get('table_name');
	   $table_id = $request->get('table_id');
	   
	   $table = DB::table('attachments');
	   
	   $have = $table->exists([
	   'file_id'=>$file_id,
	   'table_name'=>$table_name,
	   'table_id'=>$table_id
	   ]);
	   
	   if(!$have){
		   
		$values = array(
	   'file_id'=>$file_id,
	   'table_name'=>$table_name,
	   'table_id'=>$table_id
	   );
	   
	   $insert = $table->insert($values);
	   
	    return alert('Arquivo anexado.','success');
		   
	   }else{
		   
		   return alert('Esse arquivo já foi anexado.','danger');
	   }
	}	
} 