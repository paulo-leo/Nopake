<?php
namespace Modules\Dpfin\Models;

use Modules\Dpfin\Funcionario;
use Nopadi\MVC\Model;

class FuncionarioModel extends Model
{
    /*Prover o acesso estático ao modelo*/
    protected $table = "dp_funcionarios";
    
    public static function model()
    {
        return new FuncionarioModel();
    } 
    
}

