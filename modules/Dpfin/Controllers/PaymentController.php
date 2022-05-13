<?php 
namespace Modules\Dpfin\Controllers;

use Modules\Dpfin\Models\DepartamentoModel;
use Modules\Fin\Models\TitleModel;
use Modules\Fin\Models\NoteModel;
use Modules\Fin\Controllers\GroupController;
use Modules\Fin\Controllers\AccountController;
use Modules\Fin\Controllers\BrancheController;
use Modules\Fin\Controllers\RateioController;
use Modules\Fin\Models\HistoricModel;
use Nopadi\Http\URI;
use Nopadi\Http\Auth;
use Nopadi\Base\DB;
use Nopadi\Http\Send;
use Nopadi\Http\Request;
use Nopadi\Http\Param;
use Nopadi\MVC\Controller;

use Modules\Fin\Controllers\TitleController;


class PaymentController extends Controller
{
    /*Função para exibição da listagem de funcionarios*/
    public function index()
	{

        $list = DepartamentoModel::model()        
        ->paginate(20);
		
        $data = array(
            'list'=>$list
        );

        return view('@Fin/Views/titles/create?type=1', $data);
        
    }	
	
	/**/
	public function paymentTitle()
	{
		$title = new TitleController;
		
		if(access('dp_fin_pagamento'))
		{
			return $title->create(1);
		}else{
			return view('@Painel/Views/404');
		}
		
	}

    /*Função que retorna a view de criação*/
    public function create()
    {        
        $request = new Request();
        /*Identifica o tipo de título*/
        $type = $request->getInt('type');
        
        if($type == 1 || $type == 2 || $type == 3){
        
        if($type == 1 || $type == 3){
            $form_title = 'Adicionar título de pagamento';
            $participant_title = 'Fornecedor';
        }
        
        if($type == 2){
            $form_title = 'Adicionar título de recebimento';
            $participant_title = 'Cliente';
        }
        
        $branches = new BrancheController;
        $branches = $branches->brancheOptions();
        $group = new GroupController;
        $accounts = new AccountController;
        $accounts = $accounts->accountOptions();
        
        //Classificação financeira
        $group1 = $group->groupOptions(2);
        
        //Centro de custo
        $group2 = $group->groupOptions(3);
 
        $request = new Request;
        $data = array(
        'form_title'=>$form_title,
        'participant_title'=>$participant_title,
        'type'=>$type,
        'doc_type'=>$this->docType(),
        'get_payment_form'=>$this->getPaymentForm(),
        'group1'=>$group1,
        'group2'=>$group2,
        'branches'=>$branches,
        'accounts'=>$accounts
        );
        
        $rateios = new RateioController;
        
        return view("@Dpfin/Views/pagamento/create",$data);
        
        }else{
            return view("@Painel/Views/404");   
        }
    }

    /*Tipo adicional de documento*/
	public function docType($type=null)
	{
		$types = array( 
           1=>'1 - Boleto',
		   2=>'2 - Transferência',
		   3=>'3 - Salário',
		   4=>'4 - GARE',
		   5=>'5 - IPVA',
		   6=>'6 - DPVAT',
		   7=>'7 - DARF',
		   8=>'8 - GPS',
		   9=>'9 - FGTS',
		   10=>'10 - Diversos'
		);
		return is_null($type) ? $types : $types[$type];
	}

    public function getPaymentForm($status=null)
    {
         $status_name = array(
         '1'=>'1 - Conta Corrente',
         '3'=>'3 - DOC', 
         '5'=>'5 - Poupança',
         '41'=>'41 - TED Outra Titularidade',
         '43'=>'43 - TED Mesma Titularidade',
         '30'=>'30 - Títulos de cobrança do próprio banco',
         '31'=>'31 - Títulos de cobrança de outros bancos',
         '11'=>'11 - Tributos com código de barras',
         '13'=>'13 - Pagamentos de concessionárias',
         '16'=>'16 - Tributo DARF Normal',
         '17'=>'17 - Tributo GPS',
         '18'=>'18 - Tributo DARF Simples',
         '21'=>'21 - Tributo DARJ',
         '22'=>'22 - Tributo GARE SP ICMS',
         '23'=>'23 - Tributo GARE SP DR',
         '24'=>'24 - Tributo GARE SP ITCMD',
         '25'=>'25 - IPVA',
         '26'=>'26 - Licenciamento',
         '27'=>'27 - DPVAT',
         '404'=>'Não identificado');
             
             
         if(!is_null($status) && !array_key_exists($status,$status_name))
         {
             $status = '404';
         }
             
             
             return is_null($status) ? $status_name : $status_name[$status];
    }
    
    /*Formulário de pagamento de FGTS*/
    public function formFGTS()
    {
      
        $branches = new BrancheController;
        $branches = $branches->brancheOptions();
        $group = new GroupController;
        $accounts = new AccountController;
        $accounts = $accounts->accountOptions();

        $data = array(
        'doc_type'=>$this->docType(),
        'get_payment_form'=>$this->getPaymentForm(),
        'branches'=>$branches,
        'accounts'=>$accounts
        );

        return view("@Dpfin/Views/pagamento/create-fgts",$data);

    }

} 