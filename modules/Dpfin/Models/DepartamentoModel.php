<?php
namespace Modules\Dpfin\Models;

use Modules\Dpfin\Departamento;
use Nopadi\MVC\Model;

class DepartamentoModel extends Model
{
    /*Prover o acesso estático ao modelo*/
    protected $table = "dp_departamento";
    
    public static function model()
    {
        return new DepartamentoModel();
    } 
    
}

