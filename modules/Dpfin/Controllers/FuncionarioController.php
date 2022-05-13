<?php 
namespace Modules\Dpfin\Controllers;

use Nopadi\Base\DB;
use Nopadi\Http\Request;
use Nopadi\Http\Param;
use Nopadi\MVC\Controller;

use Modules\Fin\Controllers\ProviderController;
use Modules\Fin\Models\ProviderModel;


class FuncionarioController extends Controller
{
    private $update = 0;
    private $create = 0;
    private $update_error = 0;
    /*Função para exibição da listagem de funcionarios*/
    public function index()
	{
       if(access('dp_fin_funcionario'))
	   {
          $f = new ProviderController;
	       return $f->index(3);
	   }else{
		   return view('@Painel/Views/404');
	   }
        
    }	

    /*Importa e atualiza funcionário por meio de arquivo TXT*/
    public function importForm()
    {
		if(access('dp_fin_funcionario_importar'))
	   {
           return view('@Dpfin/Views/funcionario/import');
	   }else{
		    return view('@Painel/Views/404');
	   }
    }

     /*Processa importação de funioários via TXT*/
     public function importStore()
     {
        $request = new Request;
     
        $type = $request->get('type',1);
        $file = $request->getFile('userfile','tmp_name');
        $file = $this->convertFileToArray($file,$type);
        $this->updateFun($file);
        $msg = "{$this->update} atualizados, {$this->update_error} Não atualizados e {$this->create} Criados";

        return alert($msg);
     }

    private function updateFun($file){
        $model = new ProviderModel;
        foreach($file as $value)
        {
           $cpf = $value['cpf_cnpj'];

           if(validate_cpf($cpf)){
           $id = $model->exists(['cpf_cnpj'=>$cpf]);
           if($id)
           {
              
              $update = $model->update($value,$id); 
              $this->update =  $this->update + 1;

           }else{
              
               $value['type'] = 3;
               $create = $model->insert($value); 
               $this->create =  $this->create + 1;
           }

          }
       }

    }

    /*Retorna códigos padrões*/
    private function convertCodeB($code)
	{
		$code = str_ireplace('.','',$code);
		$code = str_ireplace(',','',$code);
		$code = str_ireplace(';','',$code);
		$code = str_ireplace('-','',$code);
		$code = str_ireplace('\\','',$code);
		$code = str_ireplace('/','',$code);
		
		$code = trim($code);
		
		$bb = '001';
		$itau = '341';
		
		$codes = array(
		  '749'=>$bb,
		  '1341'=>$itau,
		  '2341'=>$itau
		);
		
		return array_key_exists($code,$codes) ? $codes[$code] : $code;
		
	}

    /*Converte TXT em array*/
	public function convertFileToArray($file,$typeAccount)
	{  
		$file = fopen($file, 'r');
        $employees = array();
        if ($file)
		{
            while (($lines = fgets($file)) !== false)
			{
			$lines = str_ireplace('"','',$lines);
			$line = explode(',',$lines);
            $codigo = str_ireplace(['.','-','/'],'',$line[0]);
            $cpf = str_ireplace(['.','-','/'],'',$line[1]);
			$name = $line[2];
            $banco = $line[3];
            $agencia = $line[4];
            $conta = $line[5];
			
			$banco = $this->convertCodeB($banco);

            $conta = explode('-',$conta);
            $conta_numero = $conta[0];
            $conta_digito = isset($conta[1]) && strlen(trim($conta[1])) > 0 ? $conta[1] : 0;

			
			$line[2] = str_ireplace('.','',$line[2]);
			$value = isset($line[3]) ? $line[2].'.'.$line[3] : $line[2];
			$employees[] = array(
             'code'=>$codigo,
			 'cpf_cnpj'=>$cpf,
			 'name'=>$name,
             'account_type'=>$typeAccount,
             'agency_digit'=>0,
			 'bank_code'=>$banco,
             'agency_number'=>$agencia,
             'account_number'=>$conta_numero,
             'account_digit'=>$conta_digito
			);
           }
           fclose($file);
       }
       return $employees;	  
	}
	
	/*Função que retorna a view de edição*/
    public function edit()
    {        
       if(access('dp_fin_funcionario_editar'))
	   {
		$c = new ProviderController;
        return $c->edit();
	   }else{
		   return view('@Painel/Views/404');
	   }
    }
	
	 /*Função que retorna a view de visualiação*/
    public function show()
    {
	   if(access('dp_fin_funcionario'))
	   {
        $c = new ProviderController;
        return $c->show();
	   }else{
		  return view('@Painel/Views/404'); 
	   }
    }
} 