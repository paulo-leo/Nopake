<?php 
namespace Modules\Dpfin; 

use Nopadi\Base\DB;
use Nopadi\Http\Route;
use Nopadi\MVC\Module;
use Nopadi\Http\Request;
use Modules\Fin\Models\TitleModel;

class Dpfin extends Module
{
    public function main()
	{
	
	  if(access('dp_fin_diaria_notification'))
	  {
		  Route::get('dpfin/diaria/notifications', '@Dpfin/Controllers/DiariaController@Notifications'); 
	  }
	 
      if(access('dp_fin_access')){
       
	  Route::get('dpfin',function(){			
			return view('@Dpfin/Views/index');			
		});
		
	   
		$paper = array(
			'post:dpfin/folha/loadInfo'=>'loadFolhaInfo',
			'post:dpfin/folha/updateValue'=>'updateLiquidValue',
			'post:dpfin/folha/deleteInfo'=>'destroyInfo',
			'post:dpfin/folha/toTrash'=>'trashcan',
			'post:dpfin/folha/close'=>'close',
			'dpfin/folha/import'=>'import',
			'post:dpfin/folha/store-import'=>'storeImport'
		);

		$taker = array(
			'dpfin/tomadores/diaria'=>'diariaTeste'
		);
		
		 $diaria = array(
		 'post:dpfin/diaria/remove/item'=>'removeDiaria',
		 'dpfin/diaria/txt'=>'txt',
		 'post:dpfin/diaria/payment-insert'=>'sendPaymentsInsert',
		 'dpfin/diaria/payment'=>'sendPayments',
		 'post:dpfin/diaria/payment'=>'sendPaymentsStore',
		 'post:dpfin/diaria/aprovar'=>'aprovar',
		 'dpfin/diaria/listar-txt'=>'listarTxt',
		 'post:dpfin/diaria/negar'=>'negar',
		 'post:dpfin/diaria/lancar'=>'lancar',
		 'dpfin/diaria/code/{code}'=>'show',
		 'dpfin/diaria'=>'todas',
		 'dpfin/diaria/create'=>'create',
		 'post:dpfin/diaria'=>'addItem',
		 'post:dpfin/diaria/publicar'=>'publicar',
		 'dpfin/diaria/list'=>'listItens',
		 'post:dpfin/diaria/export'=>'export'
		);
		
		
        $funcionarios = array(
			'dpfin/funcionario/import'=>'importForm',
			'post:dpfin/funcionario/import'=>'importStore'
		);

		Route::controllers($funcionarios, '@Dpfin/Controllers/FuncionarioController');
		
		Route::controllers($diaria, '@Dpfin/Controllers/DiariaController');
		Route::get('dpfin/providers', '@Dpfin/Controllers/DPFinController@showProviders');
		Route::controllers($paper, '@Dpfin/Controllers/FolhaController');
		Route::controllers($taker, '@Dpfin/Controllers/TomadorController');
		Route::resources('dpfin/tomadores', '@Dpfin/Controllers/TomadorController');


        $payments = array(
		'dpfin/payment_title'=>'paymentTitle',
		'dpfin/create/beneficios'=>'paymentBeneficio',
        'dpfin/pagamento/create/fgts'=>'formFGTS'
	    );

		Route::controllers($payments, '@Dpfin/Controllers/PaymentController');
		
		$departamentos = array(
		'dpfin/departamentos'=>'index',
		'dpfin/departamentos/create'=>'create'
		);
		

        $beneficios = array(
		  'post:dpfin/beneficios/update'=>'updateBenefico',
		  'post:dpfin/beneficios/update-status-ok'=>'updateStatusOK',
		  'post:dpfin/beneficios/update-status-close'=>'updateStatusClose',
		  'post:dpfin/beneficios/search'=>'search',
		  'dpfin/beneficios'=>'index',
		  'dpfin/beneficios/{id}'=>'show',
	      'dpfin/beneficios/import/form'=>'importForm',
		  'post:dpfin/beneficios/import'=>'importStore'
		);

        Route::controllers($beneficios,'@Dpfin/Controllers/BeneficiosController');

		Route::controllers($departamentos, '@Dpfin/Controllers/DepartamentoController');
		Route::resources('dpfin/pagamento', '@Dpfin/Controllers/PaymentController');
		Route::resources('dpfin/funcionario', '@Dpfin/Controllers/FuncionarioController');	
		Route::resources('dpfin/folha', '@Dpfin/Controllers/FolhaController');			
    }
  }
} 
