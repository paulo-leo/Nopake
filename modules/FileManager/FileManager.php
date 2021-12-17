<?php 
namespace Modules\FileManager; 

use Nopadi\Http\Auth;
use Nopadi\Http\Route;
use Nopadi\MVC\Module;

class FileManager extends Module
{
	public function main()
	{

	   if(Auth::check('admin')){

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
			'post:store-file'=>'storeFile'
		);

       
	    Route::resources('filemanager/folders','@FileManager/Controllers/FolderManagerController');

		Route::controllers($fileManagement,'@FileManager/Controllers/FileManagerController');


		Route::controllers($file,'@FileManager/Controllers/FileController');
		Route::controllers($folder,'@FileManager/Controllers/FolderController');

		//Route::resources('inventory/products','@inventory/Controllers/ProductController');

	  }

	}
} 
