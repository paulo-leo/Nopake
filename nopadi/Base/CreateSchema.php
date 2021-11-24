<?php

namespace Nopadi\Base;

use Nopadi\Http\URI;
use Nopadi\Base\DB;
use Nopadi\Base\Schema;
use Nopadi\Base\Connection;
use PDO;

class CreateSchema extends Schema
{
	private $cmd;
	private $data;
	
	public function create()
    {
		
	}
	
	public function modify()
    {
		
	}
	
	/*Modifica o schema*/
	/*Cria um novo schema em cima do schema atual*/
	public function executeAll($create='create'){
		$con = Connection::getConn();
		if($con){

			$uri = new URI;
			$pasta = $uri->local('app/Models/Schemas/');
			
			if(is_dir($pasta))
             {
              
			  $diretorio = dir($pasta);

               while(($arquivo = $diretorio->read()) !== false)
               {
				   
				   if(strlen($arquivo) > 3){
					   
					    $classe =  str_ireplace('.php','',$arquivo);
						$controller = "App\\Models\\Schemas\\".$classe;
						$this->cmd .= call_user_func_array(array(new $controller, $create), array_values([]));
				   }

				}

                $diretorio->close();

			     if(DB::executeSql($this->cmd)){
					 $this->createFile();
		            $this->createFile(true);
					if($create == 'create') hello('Schema excutado com sucesso','success');
				    else  hello('Schema moficado com sucesso','success');	
				 }else hello('Schema não executado','danger');
             }
            else
            {
               hello('Diretório não localizado.','danger');
             }
		}else{
			 hello('Não há conexão com o banco de dados.','danger');
		}
		
	}
	
	/*Método responsável por restornar a estrutura de tabela existentes no sistema*/
	public function showMysql(){
		
		$sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".NP_DB_NAME."'";
		
		
		$query = DB::sql($sql);

		$valor;
		foreach($query as $table){
			extract($table);
			
			if($COLUMN_KEY == 'MUL' && $EXTRA != 'auto_increment'){
				$EXTRA = "<b style='color:red'>key</b> ";
			}
			
			if($EXTRA == 'auto_increment'){
				$EXTRA = "<b style='color:red'>key</b> ";
			}
			
			if($DATA_TYPE == 'longtext'){
				$CHARACTER_MAXIMUM_LENGTH = null;
			}
			
			if(!is_null($CHARACTER_MAXIMUM_LENGTH)){
				$CHARACTER_MAXIMUM_LENGTH = "(<span class='text-blue'>{$CHARACTER_MAXIMUM_LENGTH}</span>)";
			}
			
			if(is_null($COLUMN_DEFAULT) && $IS_NULLABLE != 'NO'){
				$COLUMN_DEFAULT = " <b style='color:blue'>null</b>";
			}else{
				if(!is_null($COLUMN_DEFAULT)){$COLUMN_DEFAULT = " <span class='small text-red'>(".$COLUMN_DEFAULT.")</span>";}
			}
			
			$valor[$TABLE_NAME]['name'] = $TABLE_NAME;
			$valor[$TABLE_NAME]['rows'][$COLUMN_NAME] 
			= $EXTRA.$COLUMN_NAME.' <sub class="text-green">'.$DATA_TYPE.$CHARACTER_MAXIMUM_LENGTH.'</sub>'.$COLUMN_DEFAULT;
		}
		$table = null;
		foreach($valor as $query){
		
		 $table_name = $query['name'];

		 
		 $table .= "<div class='col m3 padding animate-zoom'><div class='white link hover-pale-yellow card'>";
		  $table .= "<h3 class='bg-1 center border-bottom'>{$table_name}</h3>";
		  $table .= "<ol class=''>";
			foreach($query['rows'] as $rows){
			  $table .= "<li>{$rows}</li>";
		     }
		$table .= "</ol>";
		 $table .= "</div></div>";
		}
		
		return $table;
	}
	
    /*Método responsável por criar um arquivo com a query SQL realizada*/
	public function createFile($roll=false){
		 $dir = new URI;
	     $dir = $dir->local('storage/schemas/');
		 if(!is_dir($dir)) mkdir($dir,0755,true);
		 
		 if($roll){
			$path = $dir.'/rollback_schema.php'; 
		 }else{
			 $path = $dir.'/'.date('Y_m_d_H_i_s').'_schema.php'; 
		 }
			  $content = "<?php /*".$this->cmd."*/ ?>";
		      $file = fopen($path,'w');
			  if(fwrite($file,$content)){
						  fclose($file);
		       }
	}
	
}
