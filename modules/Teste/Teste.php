<?php 
namespace Modules\Teste; 

use Nopadi\Base\Table;
use Nopadi\MVC\Module;

class Teste extends Module
{
	/*Este é método principal para execução do módulo enquanto ativo*/
	public function main()
    {
		
    }
	
	/*Este método será executado na ativação do módulo*/
	public function active()
    {
		
        $table = new Table;
		$ar = [
			'teste1'=>[
				'id|primary_key',
				'nome|string|size:10'
			]
		]; 
		$table->create($ar);
		
    }
	
	/*Este método será executado na desativação do módulo*/
	public function disabled()
    {
		$table = new Table;
		$table->drop('teste1');
    }
	
	/*Este método será executado na atualização do módulo*/
	public function update()
    {
        
    }
} 
