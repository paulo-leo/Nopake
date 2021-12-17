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

class FolderManagerController extends Controller
{
	public function index()
	{
		
	}
	
	public function create()
	{
		return view("@FileManager/Views/folder-create");
	}
} 