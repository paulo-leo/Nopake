<?php 
namespace Modules\Dpfin\Controllers;

use Nopadi\MVC\Controller;
use Nopadi\Http\Request;
use Nopadi\Http\Param;
use Nopadi\Base\DB;
use Modules\Fin\Controllers\BrancheController;

class BeneficiosController extends Controller
{
	private $errors_msg = null;
	private $dados_importados = array();

    public function index()
	{
	  if(access('dp_fin_beneficio')){
      $table = DB::table('dp_beneficios')->as('b')
	 ->select('b.*')
	 ->select(['t.name as tomador','f.name as filial'])
	 ->leftJoin('fin_branches f','f.id','b.branche_id') 
	 ->leftJoin('fin_providers t','t.id','b.taker_id') 
	 ->orderBy('b.id desc')
	 ->paginate();

	  $data = array(
		  'list'=>$table
	  );

	   return view('@Dpfin/Views/beneficio/index',$data);	
	  }else{
		  return view('@Painel/Views/404');
	  }
	}
	
	/*Atualiza a folha para aprovada*/
	public function updateStatusOK()
	{
       
	   $request = new Request;
 
	   $id = $request->get('id'); 	   
	   $beneficio = DB::table('dp_beneficios')->find($id);
	   
	   $msg = "Erro ao executar ação. ";
	   if($beneficio)
	   {
		  if($beneficio->status == 1)
		  {
			 DB::table('dp_beneficios')->update(['status'=>5],$id);   
			 
			 $msg = "Beneficio aprovado com sucesso. ";
		  }  
	   }
	   return $msg;
	}
	
	/*Atualiza mais detalhes sobre os beneficios*/
	public function updateBenefico()
	{

		$request = new Request;
        $id = $request->get('id');
		$file_id = $request->get('file_id');
		
		$update = array(
		  'file_id'=>$file_id
		);
		
		$update = DB::table('dp_beneficios')->update($update,$id);
		
		if($update){
			return alert('Folha de benéficos  atualizada com sucesso.','success');
		}else{
			return alert('Falha ao atualizar folha de benéficos.','danger');
		}
		
	}
	
	
	/*Atualiza a folha para fechada*/
	public function updateStatusClose()
	{
       
	   $request = new Request;
 
	   $id = $request->get('id'); 	   
	   $beneficio = DB::table('dp_beneficios')->find($id);
	    $msg = "Erro ao executar ação. ";
	   if($beneficio)
	   {
		  if($beneficio->status == 2)
		  {
			 DB::table('dp_beneficios')->update(['status'=>5],$id);   
			 $msg = "Folha de beneficio fechada com sucesso.";
		  }  
	   }
		return $msg;
	}


    public function search()
	{
        $request = new Request;
        $page = $request->get('page');
        $search = $request->get('search');
    
        $datas = DB::table('dp_beneficios');
    
        if(strlen(trim($search)) > 1)
        {
			 $sql = "SELECT e.name as e_name, b.id,b.status,b.type,t.name FROM dp_beneficios b LEFT JOIN fin_providers t ON t.id = b.taker_id LEFT JOIN fin_branches e ON e.id = b.branche_id WHERE b.status = 5";
        }else{
            $sql = "SELECT e.name as e_name, b.id,b.status,b.type,t.name FROM dp_beneficios b LEFT JOIN fin_providers t ON t.id = b.taker_id LEFT JOIN fin_branches e ON e.id = b.branche_id WHERE b.status = 5";
        }
    
        $datas = $datas->firstQuery($sql);
        $datas = $datas->paginate();
    
        $data = null;
    
        foreach ($datas->results as $values) 
        {
            extract($values);

             $type = $type == 1 ? 'VT' : 'VR';

            $data[] = array("id"=>$id, "text"=>"[{$id} {$type}] {$name} ({$e_name})");
        }
            
        $data[] = array("paginate"=>true);
            
        return json($data);
    }

	public function show()
	{
	  $id = Param::int('id');
	  
	  $beneficio = DB::table('dp_beneficios')->as('b')
	  ->select(['b.id','b.type','t.name','b.status','b.file_id'])
	  ->where('b.id',$id)
	 ->leftJoin('fin_providers t','t.id','b.taker_id')
	 ->get('o');
	  
      $table = DB::table('dp_beneficios_itens')->as('i')
	  ->select('i.*')
	  ->select('f.cpf_cnpj as cpf, f.name,f.code')
	  ->leftJoin('fin_providers f','f.id','i.funcionario_id')
	 ->where('beneficio_id',$id) 
	  ->get();

	  $data = array(
		  'items'=>$table,
		  'id'=>$id,
		  'beneficio'=>$beneficio
	  );

	   return view('@Dpfin/Views/beneficio/show',$data);	
	}

    /*Exibe um formulário para importação de beneficios*/
    public function importForm()
	{


    	$branches = new BrancheController;

		$data = array(
		 'branche_class'=>$branches
		);


      return view('@Dpfin/Views/beneficio/import',$data);
    }	
	
  

	public function importStore()
	{

		$request = new Request;

        $id_taker = $request->getInt('id_taker');
        $description = $request->getString ('description');
        $type = $request->getInt('type');
		$taker_id = $request->getInt('taker_id');
		$branche_id = $request->getInt('branche_id');
		$description =  $request->get('description');

		
			$file = $request->getFile('userfile','tmp_name');
			$funcionarios = $this->convertFileToArray($file);

			if($this->validarFuncionario($funcionarios))
			{
          
                $id = DB::table('dp_beneficios')->insert([
					'status'=>1,
					'type'=>$type,
					'taker_id'=>$taker_id,
					'branche_id'=>$branche_id,
					'description'=>$description
				]);

				$id = DB::table('dp_beneficios_itens')->insertMultiple($this->getDados($id));

				if($id){
                        return alert('Benefícios importados com sucesso','success');
				}else{
					   return alert('Erro ao importarBenefícios.','danger');
				}
			}else{
               return alert($this->getErrors(),'danger');
			}	
	}

   /*Faz a validação dos funcionários*/
   public function validarFuncionario(array $funcionarios)
   {
	   $msg_erros = null;
	   $importacoes = array();
	   
       foreach($funcionarios as $funcionario)
	   {
          $cpf = not_mask($funcionario['cpf']);
		  $nome = $funcionario['nome'];
		  $tipo = $funcionario['tipo'];
		  $tarifa = $funcionario['tarifa'];
		  $diaria = $funcionario['diaria'];
		  $valor = $funcionario['valor'];

		  
          $sql = DB::table('fin_providers')->select(['id','name'])->where('cpf_cnpj',$cpf)->get('o');
		  
          if($sql){
            
			$importacoes[] = array(
				'tarifa'=>$tarifa,
				'funcionario_id'=>$sql->id,
				'tipo'=>$tipo,
				'diaria'=>$diaria,
				'valor'=>$valor
			);
            


		  }else{
            $msg_erros .= "<br>Funcionário: <b>{$nome}</b> de CPF <b>{$cpf}</b> não cadastrado no sistema.<br>";
		  }
	   }
	    $this->errors_msg = $msg_erros;
        $this->dados_importados = $importacoes; 

		return  is_null($this->errors_msg) ? true : false;
   }

   private function getErrors()
   {
	   return $this->errors_msg;
   }

   private function getDados($id=0)
   {
	  for($i=0;$i<count($this->dados_importados);$i++)
	  {
		$this->dados_importados[$i]['beneficio_id'] = $id; 
	  }
      return $this->dados_importados;
   }

	/*Converte folha de benefícios dos funcionários em uma matriz multidimensional */
	public function convertFileToArray($file)
	{
		$file = fopen($file, 'r');
        $employees = array();
        if ($file)
		{
            while (($lines = fgets($file)) !== false)
			{
			$lines = str_ireplace('",','|',$lines);
			$lines = str_ireplace(',"','|',$lines);
			$lines = str_ireplace('"','',$lines);
			$line = explode('|',$lines);
			$tarifa = $line[0];
             

            $cpf = str_ireplace(['.','-','/'],'',$line[1]);
			$name = $line[2];
			$diaria = $line[3];

			$valor = str_ireplace(',','.',$line[4]);

			$tipo_de_pagamento = contains($tarifa,'EM ESPECIE') ? 1 : 2;
			
			$employees[] = array(
		    'tarifa'=>$tarifa,
			 'cpf'=>$cpf,
			 'nome'=>$name,
			 'diaria'=>$diaria,
			 'valor'=>floatval($valor),
			 'tipo'=>$tipo_de_pagamento
			);
           }
           fclose($file);
       }
       return $employees;	  
	}

} 
