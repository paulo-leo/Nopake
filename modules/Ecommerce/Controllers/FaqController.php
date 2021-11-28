<?php

namespace Modules\Ecommerce\Controllers;

use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Modules\Ecommerce\Models\FaqModel;
use Modules\Ecommerce\Models\CategoryModel;
use Modules\Ecommerce\Controllers\CategoryController;

class FaqController extends Controller
{
	/*Exibe a página principal*/
	public function index()
	{
		$faqs = new FaqModel;
		$faqs =  $faqs
		   ->as('f')
			->select((['f.*','c.name as category_name']))
			->leftJoin('so_categories c','c.id','f.category_id')
			->paginate();

			// var_dump($faqs);
		return view('@Ecommerce/Views/faqs/list', ['faqs' => $faqs]);
	}

	/*Exibe o formulário para edição*/
	public function edit()
	{
		$id = Param::int('id');

		if ($id) {
			$model = new FaqModel;
			$categories = new CategoryController;
			$data = $model->find($id);
			$categories = $categories->getTypes();
			$dataArray = array('data' => $data, 'categories' => $categories);
			return view('@Ecommerce/Views/faqs/edit', $dataArray);
		}
	}

	/*Processa os dados vindos do formulário de edição*/
	public function update()
	{
		$request = new Request;
		$requestId = $request->getInt('id');
		$faq_order = $request->getInt('faq_order');
		$faq_code = $request->getString('faq_code', '1:20', ' - Código inválido');
		$faq_question = $request->getString('faq_question','10:250', ' - Pergunta inválida' );
		$faq_reply = $request->getString('faq_reply', '10:250' ,' - Resposta inválida');
		$category_id = $request->getInt('category_id');
		$faq_status = $request->getBit('faq_status');

		// VALIDAÇÃO
		if($request->checkError())
		{
			$model = new FaqModel;

			$model->faq_code = $faq_code;
			$model->faq_order = $faq_order;
			$model->faq_question = $faq_question;
			$model->faq_reply =$faq_reply;
			$model->category_id = $category_id;
			$model->faq_status = $faq_status;
			$model->update($model, $requestId);
			return alert('Dados alterados com Sucesso');

		} else {	
			return alert($request->getErrorMessage(),'error');
		}
		
	}

	/*Exibe a página de criação*/
	public function create()
	{
      $category = new CategoryModel;
		//$category = $category->getCategories(null);  
		$categorycon = new CategoryController; 
		$categorycon = $categorycon->getTypes();
	
		
		
		$data = array(
			'categories'=>$category
		);

		if($category != []) {
			 return view('@Ecommerce/Views/faqs/create',$data);
		}else{

			$data = array(
				'msg_error'=>'Para criação de um Faq é necessário primeiro a criação de uma categoria de categoria'.$category ,
				'type'=>$category
			);

		   return view('@Ecommerce/Views/templates/error',$data);
			//var_dump($type_name);
		}	
	}

	/*Salva os dados vindos da pagina de criação*/
	public function store()
	{
		$request = new Request;
		$faq_code = $request->getString('faq_code','1:20',' - Código inválido');
		$faq_order = $request->getInt('faq_order');
		$faq_question = $request->getString('faq_question','10:250', ' - Pergunta inválida');
		$faq_reply = $request->getString('faq_reply','10:250', ' - Resposta inválida');
		$category_id = $request->getInt('category_id');
		$faq_status = $request->getBit('faq_status');

		// VERIFICA C JA EXISTE UM FAQ_CODE
		$request->getUnique('faq_code','so_faqs');

		// VERIFICA ERROS
      if($request->checkError())
		{
			$model = new FaqModel;
			$model->faq_code = $faq_code;
			$model->faq_question = $faq_question;
			$model->faq_reply = $faq_reply;
			$model->category_id = $category_id;
			$model->faq_status = $faq_status;
			$model->faq_order = $faq_order;
			
			if($model->save()) {
            return alert("Faq {$faq_code} criado com sucesso!","success");  
			} else{
				return alert("Erro ao tentar criar faq","error"); 
			}
		} else{

			return alert($request->getErrorMessage(),'error');

		}
	}
}
