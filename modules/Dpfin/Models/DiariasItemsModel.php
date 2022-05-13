<?php
namespace Modules\Dpfin\Models;

use Modules\Dpfin\Departamento;
use Nopadi\MVC\Model;

class DiariasItemsModel extends Model
{
    /*Prover o acesso estático ao modelo*/
    protected $table = "dp_diarias";
    
    public static function model()
    {
        return new DiariasItemsModel();
    } 
    
}
