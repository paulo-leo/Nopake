<?php
namespace Modules\Dpfin\Models;

use Modules\Dpfin\Folha;
use Nopadi\MVC\Model;

class FolhaModel extends Model
{
    /*Prover o acesso estático ao modelo*/
    protected $table = "dp_folha";
    
    public static function model()
    {
        return new FolhaModel();
    } 
    
}

