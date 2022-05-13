<?php 

namespace Modules\Dpfin\Controllers;

use Modules\Dpfin\Models\DiariasItemsModel;
use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Base\DB;
use Nopadi\Http\Send;
use Nopadi\Http\Request;
use Nopadi\Http\Param;
use Nopadi\MVC\Controller;
use Modules\Fin\Models\ProviderModel;
use Modules\Fin\Controllers\BrancheController;
use Modules\Dpfin\Controllers\FolhaController;



class DiariaController extends Controller
{
	
	 private function totalD(){
		 
		 $total = DB::table('dp_diarias')
		->where('status',1)
		->count();
		
		$msg = null;
		if($total >= 1)
		{
		
           $msg = $total > 1 ? "Existem {$total} diárias pendentes de aprovações. {$time}\n" : "Existe uma diária pendente de aprovação. {$time}\n";
		}
		
		return $msg;
	 }
	
	public function Notifications()
	{  
      header('Content-Type: text/event-stream');
      header('Cache-Control: no-cache'); // recommended to prevent caching of event data.

/**
 * Constructs the SSE data format and flushes that data to the client.
 *
 * @param string $id Timestamp/id of this connection.
 * @param string $msg Line of text that should be transmitted.
 */
    function sendMsg($id, $msg)
    {
	  if(!is_null($msg)){
		echo "retry: 60000" . PHP_EOL;
        echo "id: $id" . PHP_EOL;
        echo "data: $msg" . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
        flush();
	  }
    }

$serverTime = time();

sendMsg($serverTime, $this->totalD());
	}
	
	public function removeDiaria()
	{
		$request = new Request;
		$id = $request->getInt('id');
		
		if($id){
	       
		   $diaria = DB::table('dp_diarias')->delete($id);
			
		}		
	
	}
	
	public function sendPayments()
	{
		
		$branches = new BrancheController;
		$code = date('sYmdsi');

        $request = new Request;

        $types = array(1=>'Diária');

		$data = array(
		 'branche_class'=>$branches,
         'type'=>1,
         'type_name'=>1
		);
		return view('@Dpfin/Views/diarias/payment', $data);		
	}
	
	public function sendPaymentsInsert()
	{
	   $request = new Request;
       $ids = $request->getList('diarias',1);	   
		
	   if($request->checkMessages()){
	   
	   $tables = DB::table('dp_diarias')->as('d')
	   ->select(['d.id','d.total as value'])
	   ->select(['f.cpf_cnpj as cpf','f.name as name'])
	   ->where('d.id','in',$ids)
	    ->where('d.status',2)
	   ->leftJoin('fin_providers f','f.id','d.id_provedor')
	   ->get('oa');
	   
	   $employees = array();
	   $ids = array();
	   foreach($tables as $table)
	   {
		     $employees[] = array(
			 'cpf'=>$table->cpf,
			 'name'=>$table->name,
			 'value'=>floatval($table->value)
			);
			$ids[] = $table->id;
	   }
	   

	   $folhac = new FolhaController;
	   $folha =  $folhac->storeImport($employees);
	  
	     if($folhac->getSuccesss())
	     {

		  DB::table('dp_diarias')->where('id','in',$ids)->update(['status'=>5]);
		  return alert('Folha de diárias criada com sucesso','success');

	     }else{
			return $folha;
		 }
	   }else{
		   return alert('Você deve selecionar pelo menos uma diária.','danger');
	   }	
	}
	
    public function sendPaymentsStore()
	{
		//fin_branches
         $request = new Request;
		 $branche_id = $request->get('branche_id');
		 $taker_id = $request->get('taker');

		 $date1 = $request->getDateTime('date1');
		 $date2 = $request->getDateTime('date2');

		$tables = DB::table('dp_diarias')->as('d')
		->select(['d.*'])
		->select(['e.name as e_name'])
		->select(['f.name as f_name','f.code as matricula'])
		->leftJoin('fin_branches e','e.id','d.branche_id')
		->leftJoin('fin_providers f','f.id','d.id_provedor');

		$tables->where('d.branche_id',$branche_id);
		$tables->where('d.id_cliente',$taker_id);
		$tables->where('d.created_at','bet',$date1,$date2);

		$tables = $tables->where('d.status',2)->get('oa');

		//$html .= $tables->getMounted();
		
		if($tables){

		$html = "<table class='table table-success table-hover'>";


		
		$html .= "<thead><tr>";
		        $html .= "<td>Funcionário</td>";
				$html .= "<td>ID</td>";
				$html .= "<td>Matricula</td>";
				$html .= "<td>Filial</td>";
				$html .= "<td>Valor</td>";
		$html .= "</thead></tr><tbody>";
		
		foreach($tables as $table)
		{
		  $html .= "<tr>";               
			 
			 $html .= "<td>
			 <div class='form-check'>
              <input class='form-check-input' name='diarias[]' type='checkbox' value='{$table->id}' id='diarias-aprovadas-{$table->id}'>
              <label class='form-check-label' for='diarias-aprovadas-{$table->id}'>
			  {$table->f_name}
                </label>
              </div>
			 </td>";
			 
			 $html .= "<td>{$table->id}</td>";
			 $html .= "<td>{$table->matricula}</td>";
			 $html .= "<td>{$table->e_name}</td>";
			 $html .= "<td>".format_money($table->total)."</td>";
			
		  $html .= "</tr>";
			
		}
		
		 $html .= "<tr>";

		 $html .= "</tr>";
		
		 $html .= "</tbody></table>";
         
		}else{
			$date1 = format($date1,'date');
			$date2 = format($date2,'date');
			$html = "<p class='alert alert-danger'>Não há diárias aprovadas entre o período de {$date1} e {$date2} na filial e tomação selecionada.</p>";
		}
		 
		return $html;
	}
	
	public function todas()
	{
	 $request = new Request;
	 $order_by = $request->get('order_by','created_at');
	 $to_rank = $request->get('to_rank','desc');
	 $page_qtd = $request->getInt('page_qtd',25);
	 $status = $request->getInt('status',0);
	 $branche_id = $request->getInt('branche_id',0);	
		
	  $list = DiariasItemsModel::model()
	  ->as('d')
	  ->select('d.*,c.name as client_name,f.name as funcionario_name,f.code as matricula')
	  ->select('e.name as filial')
	  ->select('fs.name as substituido')
	  ->leftJoin('fin_branches e', 'e.id', 'd.branche_id')
	  ->leftJoin('fin_providers f', 'f.id', 'd.id_provedor')
	  ->leftJoin('fin_providers c', 'c.id', 'd.id_cliente')
	  ->leftJoin('fin_providers fs','fs.id','d.id_provedor_replace');
	  
	  if($branche_id != 0)
	  {
		$list = $list->where('d.branche_id',$branche_id);  
	  }
	  
	  if($status != 0)
	  {
		$list = $list->where('d.status',$status);  
	  }
	  
	  $list = $list->orderBy('d.'.$order_by.' '.$to_rank)
	  ->paginate($page_qtd);
	  
	  $branches = new BrancheController;
	  $branches = $branches->brancheOptions();
	  
	  $branches[0] = 'Todas';
	  
      $data = array(
	  	'list'=>$list,
		'branches'=>$branches,
		'branche_id'=>$branche_id,
	    'status_id'=>$status,
	    'to_rank'=>$to_rank,
	    'order_by'=>$order_by,
	    'page_qtd'=>$page_qtd,
		'status'=>$status
	  );

	    if(access('dp_fin_diaria'))
		{
			return view('@Dpfin/Views/diarias/index', $data);
		}else{
			return view('@Painel/Views/404');
		}
		
    }
	
	
	public function listarTxt(){
		
		$data = array();
		return view('@Dpfin/Views/diarias/expor', $data);
		
	}
	
	/*Negar diária */
	public function negar()
	{
	  if(access('dp_fin_diaria_aprovar')){
	  $request = new Request;
      $ids = $request->get('ids'); 
	  
	  if(strlen($ids) >= 1)
	  {
		$sql = "UPDATE dp_diarias SET status = 3 WHERE id IN({$ids}) AND status = 1";
		$sql = DB:: executeSql($sql);
		
		if($sql){
			hello('Diárias atualizadas com sucesso');
		}else{
			hello('Erro ao atualizar diárias.');
		}
	  }
	 }else{
		hello('Você não tem permissão para aprovar ou negar diárias.'); 
	 }
	}
	
	/*Aprovar diária*/
	public function aprovar()
	{
	  
	  $request = new Request;
      $ids = $request->get('ids'); 
	  if(access('dp_fin_diaria_aprovar')){
	  if(strlen($ids) >= 1)
	  {
		$sql = "UPDATE dp_diarias SET status = 2 WHERE id IN({$ids}) AND status = 1";
		$sql = DB:: executeSql($sql);
		
		if($sql){
			hello('Diárias atualizadas com sucesso');
		}else{
			hello('Erro ao atualizar diárias.');
		}
	  }}else{
		hello('Você não tem permissão para aprovar ou negar diárias.');  
	  }
	}
	
	/*Gerar o arquivo TXT de saída para o Nasajon*/
	public function txt()
	{
		
	  //$request = new Request;
      //$ids = $request->get('ids'); 
		
	    header('Content-Type:text/html; charset=UTF-8');
		header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
		$name = "Nasajon-persona-".date('Y-m-d');
		 header("Content-disposition: attachment; filename={$name}.txt");
		echo '04238747720;0180;150;00;142;005;';
		
	}
	
	/*Negar diária*/
	public function lancar()
	{
	  $request = new Request;
      $ids = $request->get('ids'); 
	  
	  if(strlen($ids) >= 1)
	  {
		$sql = "UPDATE dp_diarias SET status = 4 WHERE id IN({$ids}) AND status = 2";
		$sql = DB:: executeSql($sql);
		
		if($sql){
			hello('Diárias atualizadas com sucesso');
		}else{
			hello('Erro ao atualizar diárias.');
		}
	  }
	}

	public function show()
	{
		$code = Param::get('code');
		$sql = "select d.*,c.name as cliente,p.name as funcionario FROM dp_diarias d join fin_providers c ON c.id = d.id_cliente join fin_providers p ON p.id = d.id_provedor 
		where d.code = {$code} order by d.id desc";
		
		$sql = DB::sql($sql,'oa');
		$html = null;
		$total = 0;
		if($sql){
			$html .= "<tr>
		       <th>Funcionário</th>
		       <th>Função</th>
		       <th>Cliente</th>
		       <th>Data</th>
		       <th>QTD</th>
		       <th>Valor</th>
		       <th>VT</th>
		       <th>VR</th>
			   <th>Noturno</th>
			   <th>Insalu.</th>
		       <th>Reembolso</th>
		       <th>Situação</th>
		      <th>Total</th>
		    </tr>";
			
			foreach($sql as $data)
			{
			 $total += $data->total;
			 
			 $data->valor = format_money($data->valor);
			 $data->valor_vt = format_money($data->valor_vt);
			 $data->valor_vr = format_money($data->valor_vr);
			 $data->valor_reembolso = format_money($data->valor_reembolso);
			 $data->total = format_money($data->total);
			 
			 switch($data->status){
				case 1 : $data->status = 'Não enviado'; break; 
				case 2 : $data->status = '<b class="text-danger">Pendente</b>'; break;
				case 3 : $data->status = 'Aprovado'; break;
				case 4 : $data->status = 'Recusado'; break;
				case 5 : $data->status = 'Lançado'; break;
				 
			 }
			 
			 $html .= "<tr>
			              <td>
						  <input type='checkbox' name='id'>
						  {$data->funcionario}</td>
						  <td>ASG</td>
						  <td>{$data->cliente}</td>
						  <td>{$data->qtd}</td>
						  <td>{$data->valor}</td>
						  <td>{$data->valor_vt}</td>
						  <td>{$data->valor_vr}</td>
						  <td>{$data->noturno}</td>
						  <td>{$data->valor_reembolso}</td>
						  <td>{$data->status}</td>
						  <td>{$data->total}</td>
			           </tr>";	
			}
		$total = format_money($total);	
		$html .= "<tr>
		  <td colspan='10' style='text-align:right'><b>Total geral</b></td>
		  <td><b class='text-success'>{$total}</b></td>
		</tr>";
			
		}
		$data = array('table'=>$html,'code'=>$code);
		return view('@Dpfin/Views/diarias/show',$data);
	}

    /*Exibe formulário para criação de diária*/
    public function create()
	{
		if(access('dp_fin_diaria_criar'))
		{
			
			$branches = new BrancheController;
		   $code = date('sYmdsi');
		   $data = array(
		   'code'=>$code,
		    'branche_class'=>$branches
		  );
		  return view('@Dpfin/Views/diarias/diaria',$data);
			
		}else{
			return view('@Painel/Views/404');
		}
		
    }

    /*Cria uma nova diária*/
	public function addItem()
	{
		$request = new Request;
		$code = $request->get('code');
		$contrato = $request->get('contrato');
		$id_cliente = $request->get('id_cliente');
		$id_provedor = $request->get('id_provedor');
		
		$id_provedor_replace = $request->get('id_provedor_replace',0);
		$data_inicial = $request->getDate('data_inicial');
		$motivo = $request->getInt('motivo');
		$qtd = $request->getInt('qtd');
		$branche_id = $request->getInt('branche_id');
		$valor = $request->getFloat('valor');
		$valor_vt = $request->getFloat('valor_vt');
		$valor_vr = $request->getFloat('valor_vr');
		$calc_one = $request->getBit('calc_one');

	    $id_provedor_replace = $motivo < 2 ? 0 : $id_provedor_replace;
	
		$taxa = $valor < 1212 ? ($valor / 100) * 7.5 : 0; /*INSS*/
		$calc_one = $calc_one == 1 ? 1 : $qtd;
		$valor = $valor - $taxa;
        $total = $valor * $qtd;
		$total = $total + (($valor_vt + $valor_vr) * $calc_one);
		
		
	    $fun = new ProviderModel;
		$fun = $fun->find($id_provedor);
		
		if(!$fun)
		{
			return alert('Funcionário não localizado.','danger'); exit;
			
		}else{
			
			if($fun->taker_id != $id_cliente){
				
				return alert('Não foi possível adicionar essa diária, pois o tomador selecionado não está cadastrado para este funcionário. Por favor, regularize o cadastro do funcionário: "'.$fun->name.'" e tente novamente.','danger'); exit;
				
			}else
			{
				$office = $fun->office;
				$name = $fun->name;
			}
		}

	     
        $data = array(
		'code'=>$code,
		'id_cliente'=>$id_cliente,
		'id_provedor'=>$id_provedor,
		'id_provedor_replace'=>$id_provedor_replace,
		'motivo'=>$motivo,
		'data_inicial'=>$data_inicial,
		'qtd'=>$qtd,
		'valor'=>$valor,
		'valor_vt'=>$valor_vt,
		'valor_vr'=>$valor_vr,
		'branche_id'=>$branche_id,
		'contrato'=>$contrato,
		'total'=>$total
		);
		
		$msg = null;
		
	    if($motivo > 2 && ($id_provedor_replace == 0 || $id_provedor_replace == null))
		{
            $msg = "Você deve informar o funcionário que será substituído.";
		}


		if(is_null($msg)){
			$data = DB::table('dp_diarias')->insert($data);
		
			if($data){
				return alert("Diária para o funcionário \"{$name}\" adicionada com sucesso.","success");
			}else{
				return alert('Erro ao cadastrar item.','danger');
			}
		}else{
			return alert($msg);
		}
		
    }	

	/*Publica a diária*/
	public function publicar()
	{
		$request = new Request;
		$code = $request->get('code');
	
        $data = array(
		'code'=>$code,
	    'user_id'=>user_id(),
        'status'=>1		
		);
		
		$diaria = DB::table('dp_diarias')->exists(['code'=>$code]);
		$items = DB::table('dp_diarias_items')->exists(['code'=>$code,'status'=>1]);
		
		if(!$diaria && $items){
			
		$data = DB::table('dp_diarias')->insert($data);
		
		if($data){
			
			$sql = "UPDATE dp_diarias_items SET status = 2 WHERE code = {$code}";
			
			DB::executeSql($sql);
			
			hello('Diária publicada com sucesso');
			
		}else{
			hello('Erro ao cadastrar enviar diária.');
		}
		}else{
			hello("O código {$code} de diária já foi publicado ou não há funcionários associados a folha de inserção de diárias.");
		}
	}
	
	/*Lista itens da diária*/
	public function listItens(){
		$request = new Request;
		$code = $request->get('code');
		
		$sql = "select d.*,c.name as cliente,p.office, p.name as funcionario,f2.name as f2_name FROM dp_diarias d join fin_providers c ON c.id = d.id_cliente join fin_providers p ON p.id = d.id_provedor left join fin_providers f2 ON f2.id = d.id_provedor_replace
		where d.code = {$code} order by d.id desc";
		
		$sql = DB::sql($sql,'oa');
		$html = null;
		$total = 0;
		if($sql){
			
			foreach($sql as $data)
			{
			 $total += $data->total;
			 
			 $data->data_inicial = format($data->data_inicial,'date');
			 $data->data_final = format($data->data_final,'date');
			 
			 $data->valor = format_money($data->valor);
			 $data->valor_vt = format_money($data->valor_vt);
			 $data->valor_vr = format_money($data->valor_vr);
			 $data->total = format_money($data->total);

			 $motivos = array(
				 1=>'Nova',
				 2=>'Hunter',
				 3=>'Falta',
				 4=>'Atestado'
			 );
			 
			 $data->office = $data->office ? $data->office : 'Indefinida';
			 $data->f2_name = $data->f2_name ? $data->f2_name : '------------';
			 $code = id_to_code($data->id);
			 
			 $html .= "<tr>
			              <td>{$motivos[$data->motivo]}<br>{$code}</td>
			              <td style='word-break: normal;'>{$data->funcionario}</td>
						  <td style='word-break: normal;'>{$data->f2_name}</td>
						  <td style='word-break: normal;'>{$data->office}</td>
						  <td style='word-break: normal;'>{$data->cliente}</td>
						  <td>{$data->data_inicial}</td>
						  <td>{$data->valor_vt}</td>
						  <td>{$data->valor_vr}</td>
						  <td>{$data->valor}</td>
						  <td>{$data->qtd}</td>
						  <td>{$data->total}</td>
						  <td><button type='button' id='{$data->id}' class='btn btn-sm btn-outline-danger btn-add-diaria-remove'>Excluir</button></td>
			           </tr>";	
			}
		$total = format_money($total);	
		$html .= "<tr>
		  <td colspan='12' style='text-align:right'><b>Total geral</b></td>
		  <td><b class='text-success'>{$total}</b></td>
		</tr>";
			
		}else{
			
			$html = "<tr><td colspan='12'><h2 class='text-center'>Nenhuma diária adiciona ainda. </h2></td><tr>";
			
		}
		echo $html;
	}
	
	
	/*Exportar diárias*/
	public function export()
	{
		$request = new Request;
		
		$date1 = $request->getDatetime('date1');
		$date2 = $request->getDatetime('date2');
		$status = $request->getInt('status');
		
		
		$sql = "select d.*,c.name as cliente,p.name as funcionario FROM dp_diarias d join fin_providers c ON c.id = d.id_cliente join fin_providers p ON p.id = d.id_provedor WHERE d.status = {$status} AND d.created_at BETWEEN '{$date1}' AND '{$date2}' order by d.id desc";


        $data = DB::table('dp_diarias')->as('d')
		->join('fin_branches e','e.id','d.branche_id')
		->join('fin_providers t','t.id','d.id_cliente')
		->join('fin_providers f','f.id','d.id_provedor')
		->leftJoin('fin_providers fs','fs.id','d.id_provedor_replace')
		->select('d.*')
		->select('e.name as filial')
		->select('t.name as tomador')
		->select('f.name as funcionario,f.office as funcao,f.code as matricula')
		->select('fs.name as funcionario_substituido');

		if($status != 0){ $data->where('d.status',$status); }

		$data->where('d.created_at','bet',$date1,$date2);

		$data = $data->get('oa');

		
		
		$table = "<table border='1'>";
		    $table .= "<tread>"; 
			 
			 $table .= "<tr><td colspan='13'>Diárias</td></tr>";
			
		     $table .= "<tr>";
			 $table .= "<td>ID</td>";
			 $table .= "<td>Filial</td>";
			 $table .= "<td>Matricula</td>";
			 $table .= "<td>Funcionário</td>";
			 $table .= "<td>Função</td>";
			 $table .= "<td>Motivo</td>";
			 $table .= "<td>Substituído</td>";
			 $table .= "<td>Tomador/Cliente</td>";
			 $table .= "<td>VT</td>";
			 $table .= "<td>VR</td>";
			 $table .= "<td>Total</td>";
			 $table .= "<td>QTD</td>";
			 $table .= "<td>Total líquido</td>";
			 $table .= "<td>Situação</td>";
			 $table .= "<td>Competência</td>";
			 $table .= "<td>Data de criação</td>";
			 
		     $table .= "</tr>";
		    $table .= "<tread></tbody>";
		
		  foreach($data as $d)
		  {
			  $d->motivo = $d->motivo == 1 ? 'Normal' : 'Substituição';
			  $d->created_at = format($d->created_at,'datetime');
			  $d->data_final = format($d->data_final,'date');
			  $d->total = format_money($d->total);
			  $d->valor = format_money($d->valor);
			  $d->valor_vt = format_money($d->valor_vt);
			  $d->valor_vr = format_money($d->valor_vr);


			  switch($d->status)
			  {
				case 1 : $d->status = 'Pendente'; break;
				case 2 : $d->status = 'Aprovado'; break;
				case 3 : $d->status = 'Recusado'; break;
				case 4 : $d->status = 'Lançado'; break;
				case 5 : $d->status = 'Processado'; break;
				default : $d->status = 'Indefinido';
			  }
 
			  $d->funcao = is_null($d->funcao) ? 'Função não cadastrada' : $d->funcao;
			  $d->funcionario_substituido = is_null($d->funcionario_substituido) ?'Nenhuma substituição' : $d->funcionario_substituido;
			  
              $table .= "<tr>"; 
			   $table .= "<td>{$d->id}</td>"; 
			  $table .= "<td>{$d->filial}</td>"; 
			  $table .= "<td>{$d->matricula}</td>"; 
			  $table .= "<td>{$d->funcionario}</td>"; 
			  $table .= "<td>{$d->funcao}</td>"; 
			  $table .= "<td>{$d->motivo}</td>";
			  $table .= "<td>{$d->funcionario_substituido}</td>"; 
			  $table .= "<td>{$d->tomador}</td>";
			  $table .= "<td>{$d->valor_vt}</td>";
			  $table .= "<td>{$d->valor_vr}</td>";
			  $table .= "<td>{$d->valor}</td>";
			  $table .= "<td>{$d->qtd}</td>";
			  $table .= "<td>{$d->total}</td>";
			  $table .= "<td>{$d->status}</td>";
			  $table .= "<td>{$d->data_inicial}</td>";
			  $table .= "<td>{$d->created_at}</td>";
			  
			  $table .= "</tr>"; 
		  }
		
		$table .= "</tbody></table>";
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msexcel");
        header("Content-Disposition: attachment; filename=\"diarias.xls\"" );
        header("Content-Description: PHP Generated Data" );
		
		return $table;
    
	}
	
}






