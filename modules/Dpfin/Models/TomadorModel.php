<?php
namespace Modules\Dpfin\Models;

use Modules\Dpfin\Tomador;
use Nopadi\MVC\Model;

class TomadorModel extends Model
{
    /*Prover o acesso estático ao modelo*/
    protected $table = "Dp_tomadores";
    
    public static function model()
    {
    return new TomadorModel();
    } 
    
}

