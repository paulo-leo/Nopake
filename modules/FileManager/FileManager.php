<?php 
namespace Modules\FileManager; 

use Nopadi\Http\Auth;
use Nopadi\Http\Route;
use Nopadi\MVC\Module;
use Nopadi\Base\Table;

class FileManager extends Module
{
	public function main()
	{
	   if(access('file_manager')){

	   	$fileManagement = array(
           	'filemanager'=>'index',
			'filemanager/dir'=>'showDir',
			'filemanager/show/code'=>'showCode',
			'filemanager/import-image'=>'importImage',
            'filemanager/import-file'=>'importFile'			
       	);	
		$folder = array(
			'post:open-folder'=>'openFolder'
		);

		$file = array(
			'post:store-image'=>'storeImage',
			'post:store-file'=>'storeFile',
			'post:store-file-one'=>'storeFileOne'
		);
		
		$files = array(
			'files'=>'getFiles',
			'files/uploads'=>'getUploads',
			'post:attachment'=>'addAttachment',
			'attachments'=>'getAttachments',
			'file'=>'getFileUpload'
		);
 
        Route::controllers($files,'@FileManager/Controllers/FileIncludeController');
	    Route::resources('filemanager/folders','@FileManager/Controllers/FolderManagerController');
		Route::controllers($fileManagement,'@FileManager/Controllers/FileManagerController');
		Route::controllers($file,'@FileManager/Controllers/FileController');
		Route::controllers($folder,'@FileManager/Controllers/FolderController');


	  }

	}
	
	/*Ativação do módulo*/
	public function active()
	{
		$table = new Table;
		
		$tables = array(
		'attachments'=>array('id|primary_key','file_id|int|size:20','table_name|string|size:60','table_id|int|size:20'),
		'uploads'=>array('id|primary_key','path|string|size:200','description|string|size:100','type|string|size:5','ceated_in|timestamp','folder_id|int|size:11|null'),
		'uploads_folders'=>array('id|primary_key','name|string|size:80')
		);
		
		$table->create($tables);
	}
	
	public function disabled()
	{
	  $table = new Table;
      $table->drop(['attachments','uploads','uploads_folders']);	  
	}
} 
