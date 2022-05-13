<?php 
namespace Modules\Dpfin\Controllers;

use Modules\Dpfin\Models\FolhaInfoModel;
use Modules\Dpfin\Models\FolhaModel;
use Modules\Dpfin\Models\FuncionarioModel;
use Modules\Dpfin\Models\TomadorModel;
use Modules\Fin\Controllers\BrancheController;
use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Base\DB;
use Nopadi\Http\Send;
use Nopadi\Http\Request;
use Nopadi\Http\Param;
use Nopadi\MVC\Controller;

class FolhaController extends Controller
{
    /*Função para exibição da listagem de funcionarios*/
	private $msg_erros = null;
    private $success = false;
    public function index()
	{
		if(access(['dp_fin_folha']))
		{
        $list = FolhaModel::model()        
        ->as('f')
        ->select('f.*, t.name as nome,b.name as b_name')
        ->leftJoin('fin_providers t', 'f.tomador_id', 't.id')
        ->leftJoin('fin_branches b', 'b.id', 'f.branche_id')
		->orderBy('t.id desc')
        ->paginate(20);
		
        $data = array(
            'list'=>$list
        );
		
      
			return view('@Dpfin/Views/folha/index', $data); 
			
		}else{
			
			return view('@Painel/Views/404'); 
		}
               
    }	

    /*Função que retorna a view de criação*/
    public function create()
    {        
        return view("@Dpfin/Views/folha/create");
    }
	
	
	/*Editar folha*/
    public function edit()
    {   

		$id = Param::get('id'); 
        $folha = FolhaModel::model()->find($id);	
		$data = array('find'=>$folha);
		
        return view("@Dpfin/Views/folha/edit",$data);
    }

    /*Função que retorna a view de importação*/
    public function import()
    {
		if(access(['dp_fin_folha_importar'])){
		$branches = new BrancheController;
		$code = date('sYmdsi');

        $request = new Request;

        $type = $request->getInt('type',2);

        $types = array(1=>'Diária', 2=>'Salário', 3=>'Vale transporte', 4=>'Vale alimentação', 5=>'Vale refeição', 6=>'Rescisão', 7=>'Férias', 8=>'FGTS');

		$data = array(
		 'branche_class'=>$branches,
         'type'=>$type,
         'type_name'=>$types[$type]
		);
        return view("@Dpfin/Views/folha/import",$data);
		}else{
			return view('@Painel/Views/404'); 
		}
    }

    /*Processa a folha de pagamento dos funcionários*/
    public function storeImport($employees=null)
    {
        $request = new Request;
        $taker_id = $request->getString('taker_id');
        $branche_id = $request->getInt('branche_id');
        $description = $request->getString('description');
        $type = $request->getInt('type');
        $converted_type = $this->convertType($type);
		
		if(is_null($employees)){
             $file = $request->getFile('userfile','tmp_name');
             $employees = $this->convertFileToArray($file);
		}
        
		
		/*Cria a folha caso a validação seja bem feita com sucesso*/
	    if($this->validateEmployees($employees,$taker_id))
		{
			$data = array(
		    'tomador_id'=>$taker_id,
		    'descricao'=>$description,
		    'branche_id'=>$branche_id,
		    'status'=>1,
		    'tipo'=>$type
		   );
		   $folha_db = DB::table('dp_folha');
		   $folha_id = $folha_db->insert($data);
		   
		   if($folha_id)
		   {
			 $total = $this->insertValuesFolha($folha_id,$employees); 
    		 $folha_db->update(['valor_total'=>$total],$folha_id);
             $this->success = true;
             return alert('Importação de arquivo finalizada com sucesso.','success');			 
		   }
		}else{
			return alert($this->msg_erros,'danger');
		}
    }

    public function getSuccesss(){
        return $this->success;
    }
	
	/*Faz o armazenamento da folha no banco de dados*/
	private function insertValuesFolha($folha_id,$employees)
	{
		$total = 0;
		foreach($employees as $employee)
		{
		  extract($employee);
		  
		  $id = DB::table('fin_providers')->find(['cpf_cnpj'=>$cpf]);
		  $id = $id->id;
		  
		  $total += $value;
		  
	      $data = array(
		    'id_provedor'=>$id,
			'valor_liquido'=>$value,
			'folha_id'=>$folha_id
		  );
		  DB::table('dp_folha_info')->insert($data);
		}
		return $total;
	}
	
	
	/*Faz a validação dos campos. Caso encontre um erro a validação é interrompida e é retornado uma string*/
	public function validateEmployees($employees,$taker_id)
	{
		$errors = 0;
		foreach($employees as $data)
		{
			if(!$this->existEmployeeOnTaker($data,$taker_id)) $errors++;
		}
		
		return $errors < 1 ? true : false; 
	}
	
	/*Valida funcionário por funcionário dentro da folha*/
	public function existEmployeeOnTaker($data,$taker_id)
	{
		extract($data);
		$sql = "select f.bank_code,f.agency_number,f.agency_digit,f.account_number,f.account_digit,f.id,f.name from fin_providers f join fin_providers t ON t.id = f.taker_id WHERE f.cpf_cnpj = '{$cpf}' and f.taker_status = 0 AND t.id = '{$taker_id}'";
		
		$sql = DB::sql($sql,'A');
		
		if($sql)
		{   
			$check = 0;
			if(strlen($sql['bank_code']) < 1) $check += 1;
			if(strlen($sql['agency_number']) < 1 && !is_numeric($sql['agency_number'])) $check += 1;
			if(strlen($sql['agency_digit']) < 1 && !is_numeric($sql['agency_digit'])) $check += 1;
			if(strlen($sql['account_number']) < 1 && !is_numeric($sql['account_number'])) $check += 1;
			if(strlen($sql['account_digit']) < 1 && !is_numeric($sql['account_digit'])) $check += 1;
			
			if($check == 0)
			{
			  return true;	
			}else
			{
			  $this->msg_erros .= "{$sql['name']}({$cpf}) está com os dados bancários irregular.";
			  return false;
			}
		}else{
			$this->msg_erros .= "O funcionário <b>{$name}</b> de CPF <b>{$cpf}</b><br> não existe na tomação especificada ou não está cadastrado como funcionário ou está bloqueado no sistema.<hr>";
			return false;
		}	
	}
	
	/*Converte folha de pontos TXT em array*/
	public function convertFileToArray($file)
	{
		$file = fopen($file, 'r');
        $employees = array();
		
        if ($file)
		{
            while (($lines = fgets($file)) !== false)
			{
			$lines = str_ireplace('"','',$lines);
			$line = explode(',',$lines);
            $cpf = str_ireplace(['.','-','/'],'',$line[0]);
			$name = $line[1];
			
			$line[2] = str_ireplace('.','',$line[2]);
			$value = isset($line[3]) ? $line[2].'.'.$line[3] : $line[2];
			$employees[] = array(
			 'cpf'=>$cpf,
			 'name'=>$name,
			 'value'=>floatval($value)
			);
           }
           fclose($file);
       }
       return $employees;	  
	}

    /*Função para armazenar valores na DB*/
    public function store()
    {
        $request = new Request;

        $id_taker = $request->getInt('id_taker');
        $description = $request->getString ('description');
        $type = $request->getInt('type');
        $converted_type = $this->convertType($type);

        $validation = $this->storeValidationBenefit($converted_type, $id_taker);
        if($validation == null){
            $values = array(
                'tomador_id'=>$id_taker,
                'tipo'=>$type,
                'descricao'=>$description,
                'created_in'=>NP_DATE
            );

            if($request->checkError()){
                $insert = FolhaModel::model()->insert($values);
                $paper_id = $insert;
                $insert = $insert ? 'Folha criada com sucesso.' : 'Erro ao tentar criar a folha.';
                hello(alert($insert,'success'));
            }else{
                return alert($request->getErrorMessage(),'danger');			
            }
            
            $this->addPaperInfo($paper_id, $id_taker, $type);   
        }else{
            return alert($validation['msg'], $validation['type']);
        }
                 
    }

    /*Função que realiza a validação do benficio selecionado*/
    public function storeValidationBenefit($converted_type, $id_taker)
    {
         //Consulta que retorna os dados do tomador e o n° de funcionarios 
         $validation_data = TomadorModel::model()->firstQuery(
            "SELECT t.{$converted_type} as benefit, count(f.id) as num_employees FROM dp_tomadores t LEFT JOIN dp_funcionarios f ON t.id = f.id_tomador AND t.{$converted_type} = 1 WHERE t.id = {$id_taker}"
        )->get('o');

        //Validações para criação da folha. 1° Beneficio deve estar ativo no tomador 2° Deve haver algum funcionario com o beneficio selecionado ativo
        if($validation_data->benefit != 1)
        { 
            return array(
                'msg'=>'Este benefício está inativo no tomador',
                'type'=>'danger'
            );
            exit;
        } else if($validation_data->num_employees <= 0 )
        {
            return array(
                'msg'=>'Não há nenhum funcionario com este beneficio ativo',
                'type'=>'danger'
            );      
            exit;
        }
    }

    //Adiciona os funcionarios na tabela de relacionamento folha_info
    public function addPaperInfo($paper_id, $taker_id, $paper_type, $info=null)
    {                
        $type = $this->convertType($paper_type);

        if($type == null)
        {
            return alert('Tipo de folha não informado ou é invalido', 'danger');
            exit;
        }
        
        if($info == null)
        {
            //Consulta que busca o nome, cpf, bank_code, agency_number e agency_digit dos funcionarios que possuem o beneficio selecionado ativo e estão relecionados ao tomador selecionado
            $data = FuncionarioModel::model()->firstQuery(
                "SELECT p.name as name, p.cpf_cnpj as cpf, p.bank_code as bank, p.agency_number as agency, p.account_number as acc_number, p.account_digit as acc_digit FROM dp_funcionarios f LEFT JOIN fin_providers p ON p.id = f.id_provedor AND f.{$type} = 1 WHERE f.id_tomador = {$taker_id}"
            )->get();

            if($data == null){
                return alert('Nenhum funcionário encontrado.');
                exit;
            }

            for($i=0; $i < count($data); $i++)
            {
                $values = array(
                    'nome'=>$data[$i]['name'],
                    'cpf'=>$data[$i]['cpf'],
                    'banco'=>$data[$i]['bank'],
                    'agencia'=>$data[$i]['agency'],
                    'conta'=>$data[$i]['acc_number'].'-'.$data[$i]['acc_digit'],
                    'folha_id'=>$paper_id
                );
        
                FolhaInfoModel::model()->insert($values);  
            }
        }
		
		else
        {
            //Consulta que busca o nome, cpf, bank_code, agency_number e agency_digit dos funcionarios que possuem o beneficio selecionado ativo e estão relecionados ao tomador selecionado
            $data = FuncionarioModel::model()->firstQuery(
                "SELECT f.id_provedor as provedor, p.name as name, p.cpf_cnpj as cpf, p.bank_code as bank, p.agency_number as agency, p.account_number as acc_number, p.account_digit as acc_digit FROM dp_funcionarios f LEFT JOIN fin_providers p ON p.id = f.id_provedor AND f.{$type} = 1 WHERE f.id_tomador = {$taker_id} AND p.cpf_cnpj = {$info['cpf']}"
            )->get('o');

            if($data == null){
                return alert('Nenhum funcionário encontrado.');
                exit;
            }

            $taker = FolhaModel::model()
            ->as('f')
            ->select('t.valor_mensal as valor_max')
            ->leftJoin("dp_tomadores t", "f.tomador_id", 't.id')
            ->where('f.id', '=', $paper_id)
            ->get('o');
    
	         $money = $info['money'];
            /*
            if($info['money'] > $taker->valor_max)
            {
                $money = 0;
            }else{
                $money = $info['money'];
            }
			*/
            //$data->acc_number.'-'.$data->acc_digit
            $values = array(
                'nome'=>$data->name,
                'cpf'=>$data->cpf,
                'banco'=>!is_null($data->bank) ? $data->bank : 0,
                'agencia'=>!is_null($data->agency) ? $data->agency : 0,
                'conta'=>'77777',
                'valor_liquido'=>$money,
                'folha_id'=>$paper_id,
				'id_provedor'=>$data->provedor
            );
    
            FolhaInfoModel::model()->insert($values);  
            $this->updateTotalValue($paper_id);
        }             
        
    }

    //Converte o valor numerico do tipo para o nome do campo no banco de dados
    public function convertType($type)
    {
        switch($type)
        {
            case 1:
                $converted_type = "diaria_status";
            break;

            case 2:
                $converted_type = "salario_status";
            break;

            case 3:
                $converted_type = "vt_status";
            break;

            case 4:
                $converted_type = "va_status";
            break;

            case 5:
                $converted_type = "vr_status";
            break;

            case 6:
                $converted_type = "rescisao_status";
            break;

            case 7:
                $converted_type = "ferias_status";
            break;

            case 8:
                $converted_type = "fgts_status";
            break;

            default:
                $converted_type = null;
            break;
        }

        return $converted_type;
    }


    /* Função que retorna um JSON com as informações da folha */
    public function loadFolhaInfo()
    {
        $request = new Request;
        $id = $request->getInt('id');

        $data = FolhaInfoModel::model()
        ->as('i')
        ->select('i.*')
        ->where('i.folha_id', '=', $id)
        ->get('aa');

        return json($data);
    }

    /*Função que atualiza o valor liquido na tabela folha_info*/
    public function updateLiquidValue()
    {
        $request = new Request;
        $id = $request->getInt('id');
        $folha_id = $request->getInt('folha_id');
        $liquid_value = $request->get('value');
        
        $taker = FolhaModel::model()
        ->as('f')
        ->select('t.valor_mensal as valor_max')
        ->leftJoin("dp_tomadores t", "f.tomador_id", 't.id')
        ->where('f.id', '=', $folha_id)
        ->get('o');


        if($liquid_value > $taker->valor_max)
        {
            return alert('O valor definido para o funcionário não pode ser maior que o valor definido para o tomador.', 'warning');
            exit;
        }

        $values = array(
            'valor_liquido'=>$liquid_value
        );

        if($request->checkError()){
            $update = FolhaInfoModel::model()->update($values, $id);            
            $update = $update ? 'Valor atualizado com sucesso.' : 'Erro ao tentar atualizar o valor.';
            $this->updateTotalValue($folha_id);
            return alert($update, 'success');
        }else {
            return alert($request->getErrorMessage(),'danger');
        }
    }

    /*Função que atualiza o valor total da folha*/
    public function updateTotalValue($id)
    {
        $total_sum = FolhaInfoModel::model()
        ->as('i')
        ->select('SUM(i.valor_liquido) as valor_total')
        ->where('i.folha_id', '=', $id)
        ->get('o');

        $values = array(
            "valor_total"=>$total_sum->valor_total
        );

        FolhaModel::model()->update($values, $id);        
    }

    /*Função que retorna a view SHOW*/
    public function show()
    {
        $find = FolhaModel::model()
        ->as('f')
        ->select('f.*, t.name as t_nome')
        ->leftJoin('fin_providers t', 'f.tomador_id', 't.id')
        ->where('f.id', '=', $this->id())
        ->get('o');

        $paper_info = DB::table('dp_folha_info')->as('f')
		->select(['valor_liquido'],'f')
		->select([
		'name',
		'cpf_cnpj',
		'bank_code',
		'agency_number',
		'account_number'
		],'p')
		->leftJoin('fin_providers p','p.id','f.id_provedor')
        ->where('f.folha_id', '=', $this->id())
        ->get('oa');     
	

        $list = array(
            'find'=>$find,
            'info'=>$paper_info
        );
       
        return view("@Dpfin/Views/folha/show", $list);
    }

    /*Função que altera os valores na DB*/
    public function update()
    {               
        $request = new Request;        

        $id = $request->getInt('id'); 
        $descricao = $request->get('descricao');
        $file_id = $request->get('file_id');		

        $values = array(
            "descricao"=>$descricao,
            "file_id"=>$file_id);
			
			$update = FolhaModel::model()->update($values, $id);
			
			return $update ? alert('Folha atualizada','success') : alert('Falha na atualização','danger');

		/*
        $description = $request->getString('description');

        $values = array(
            "descricao"=>$description,
            "status"=> 2
        );

        if($request->checkError()){
            $update = FolhaModel::model()->update($values, $id);
            $update = $update ? 'Descrição atualizada com sucesso, você será redirecionado.' : 'Nada foi alterado, você será redirecionado.';
            return alert($update,'success');
        }else{
            return alert($request->getErrorMessage(),'danger');			
        }
		*/
    }

    /*Função que atualiza o status para 6 (Lixeira) */
    public function trashcan()
    {
        $request = new Request;
        $id = $request->getInt('id');

        $find = FolhaModel::model()->find($id);

        //verifica se o status está em aberto ou cancelado caso seja diferente desses 2 não há como o status mudar para lixeira
        if($find->status == 1)
        {
            $update = FolhaModel::model()->update(['status'=>6], $id);
            $update = $update ? 'Folha movida para lixeira.' : 'Erro ao mover a folha para a lixeira.';
            return $update;
        }else{
          return "Essa folha não poderá ser movida para lixeira.";
        }
      
    }
	
	
    /*Fecha a folha */
    public function close()
    {
        $request = new Request;
        $id = $request->getInt('id');

        $find = FolhaModel::model()->find($id);

        //verifica se o status está em aberto ou cancelado caso seja diferente desses 2 não há como o status mudar para lixeira
        if($find->status == 1)
        {
            $update = FolhaModel::model()->update(['status'=>2], $id);
            $update = $update ? 'Folha fechada com sucesso.' : 'Erro ao fechar folha.';
            return $update;
        }else{
          return "Essa folha não poderá ser fechada.";
        }
      
    }
	

    /*Função que deleta um item na DB*/
    public function destroy()
    {
        $request = new Request;
        $id = $request->getInt('id');
        $find = FolhaModel::model()->find($id);

        if($find->status != 6)
        {
            $msg = "Só é possível excluir a folha se ela estiver na lixeira.";
            return alert($msg, 'info');
            exit;
        }

        //Deleta todos os itens da tabela folha_info relacionados com a folha excluida
        $delete_info = FolhaInfoModel::model()->delete(["folha_id"=>$id]);

        if($delete_info)
        {
            $delete = FolhaModel::model()->delete($id);

            if($delete)
            {
                $msg = 'Folha foi deletada com sucesso';
                return alert($msg, 'success');
            }else
            {
                $msg = 'Erro ao deletar a folha';
                return alert($msg, 'danger');
            }
        }else
        {
            $msg = 'Erro ao deletar as informações da folha';
            return alert($msg, 'danger');
        }
    }

    /*Função que deleta um item da folha-info*/
    public function destroyInfo()
    {
        $request = new Request;
        $id = $request->getInt('id');
        $folha_id = $request->getInt('folha_id');

        $info_count = FolhaModel::model()
        ->as('f')
        ->select('count(i.id) as i_count')
        ->leftJoin('dp_folha_info i', 'f.id', 'i.folha_id')
        ->where('f.id', '=', $folha_id)
        ->get('o');

        if($info_count->i_count == 1)
        {
            $msg = 'Não foi possível excluir o funcionário, pois a folha deve possuir ao menos 1 funcionário.';
            return alert($msg, 'danger');
            exit;
        }

        $delete = FolhaInfoModel::model()->delete($id);

        if($delete)
        {
            $msg = 'Funcionario removido da folha.';
            return alert($msg, 'success');
        }else
        {
            $msg = 'Erro na remoção do funcionario';
            return alert($msg, 'danger');
        }

    }

    /*Retona todas as folhas prontas para conversão de pagamentos*/
    public function search()
    {
        $request = new Request;
        $page = $request->get('page');
        $search = $request->get('search');
    
        $datas = FolhaModel::model();
    
        if(strlen(trim($search)) > 1)
        {
			 $sql = "SELECT f.tipo, f.id as id, e.name as e_name, t.name as nome FROM dp_folha f JOIN fin_providers t ON t.id = f.tomador_id LEFT JOIN fin_branches e ON e.id = f.branche_id WHERE f.status = 2 AND t.id = '{$search}' OR t.name LIKE '%{$search}%'";
        }else{
            $sql = "SELECT f.tipo,f.id as id, e.name as e_name, t.name as nome FROM dp_folha f JOIN fin_providers t ON t.id = f.tomador_id LEFT JOIN fin_branches e ON e.id = f.branche_id WHERE f.status = 2";
        }
    
        $datas = $datas->firstQuery($sql);
        $datas = $datas->paginate();
    
        $data = null;
    
        foreach ($datas->results as $values) 
        {
			extract($values);
			if($tipo == 1)
			{
			  $tipo = 'Diária';	
			}elseif($tipo ==2){
				 $tipo = 'Salário';
			}else{
				 $tipo = 'Indefinido';
			}
			
            
            $data[] = array("id"=>$id, "text"=>"[{$id} - {$tipo}] ".$nome." ({$e_name})");
        }
            
        $data[] = array("paginate"=>true);
            
        return json($data);
    }

}