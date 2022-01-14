<?php
namespace Modules\FileManager\Models; 

use Nopadi\MVC\Model;

    class UploadFolderModel extends Model
    {
	  protected $table = "uploads_folders";
	  
	  public static function model()
	  {
		return new UploadFolderModel();
	  }
    }
