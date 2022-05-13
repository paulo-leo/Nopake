<?php
namespace Modules\Dpfin\Models;

use Modules\Dpfin\FolhaInfo;
use Nopadi\MVC\Model;

class FolhaInfoModel extends Model
{
    /*Prover o acesso estático ao modelo*/
    protected $table = "dp_folha_info";
    
    public static function model()
    {
        return new FolhaInfoModel();
    } 
    
}

