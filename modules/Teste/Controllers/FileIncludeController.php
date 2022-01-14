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
    public function getFiles()
    {  
	  $files = new UploadModel;
	  
	  $files = $files->select(['*'])
	  ->orderBy('id desc')
	  ->paginate(20);
	  
	  $results = $files->results;
	  $next = $files->next;
	  $previous = $files->previous;
	  
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