<?php

namespace Modules\Ecommerce\Controllers;

use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Modules\Ecommerce\Models\CategoryModel;

class CategoryController extends Controller
{
	/*Exibe a página principal*/
	public function index()
	{
		$categories = new CategoryModel;

		$types = $this->getTypes();

		$categories =  $categories
			->select(["name", "id", "type", "category_order"])
			->paginate(10);

		return view('@Ecommerce/Views/categories/list', ['categories' => $categories, 'types' => $types]);
	}

	/*Exibe o formulário para edição*/
	public function edit()
	{
		$id = Param::int('id');
		if ($id) {
			$model = new CategoryModel;
			$data = $model->find($id); 
		
			$dataArray = array('data' => $data, 'categories' => $model->getCategories($data->type));
			return view('@Ecommerce/Views/categories/edit', $dataArray);
		}
	}

	/*Processa os dados que serão atualizados*/
	public function update()
	{
		$model = new CategoryModel;
		$request = new Request;

		// FALTA IMAGEM
		$id = $request->getInt('id');
		$name = $request->getString('name','1:150', ' - Nome Inválido');
		$categoryOrder = $request->getInt('category_order');
		$categoryId = $request->getInt('category_id');
		$status = $request->getBit('status');
		$color = $request->getString('color','1:15', ' - Cor inválida');
		$categoryShow = $request->getBit('category_show');
		$description = $request->getString('description', '1:250', ' - Descrição inválida');
		$keywords = $request->getString('keywords', '1:150', ' - Palavra chave inválida');
		$categoryNew = $request->getBit('category_new');

		// $request->getUnique('name', 'so_categories');

		if($request->checkError()) {

			$model->name = $name;
			$model->category_order = $categoryOrder;
			$model->category_id = $categoryId;
			$model->status = $status;
			$model->color = $color;
			$model->category_show = $categoryShow;
			$model->description = $description;
			$model->keywords = $keywords;
			$model->category_new = $categoryNew;

			if($model->update($model, $id)) {
				return alert('Dados alterados com Sucesso','success');
			} else {
				return alert('Erro ao atualizar dados', 'error');
			}
		} else {
			return alert($request->getErrorMessage(), 'error');
		}
	}

	/*Exibe a view de criação de categoria*/
	public function create()
	{
		$request = new Request;
      $type = $request->getInt('type',1);
		$categories = new CategoryModel;

		$categories = $categories->getCategories($type);
		$categories[0] = 'Sem categoria';

		$data = array(
			'types' => $this->getTypes(),
			'categories'=>$categories,
			'type'=>$type
		);

		return view('@Ecommerce/Views/categories/create', $data);
	}

	/*Salva os dados do formulário de criação*/
	public function store()
	{
		// FALTA IMAGEM
		$model = new CategoryModel;
		$request = new Request;

		$name = $request->getString('name','1:150', ' - Nome Inválido');
		$categoryOrder = $request->getInt('category_order');
		$categoryId = $request->getInt('category_id');
		$status = $request->getInt('status');
		$type = $request->getInt('type');
		$color = $request->getString('color','1:15', ' - Cor inválida');
		$categoryShow = $request->getBit('category_show');
		$description = $request->getString('description', '1:250', ' - Descrição inválida');
		$keywords = $request->getString('keywords', '1:150', ' - Palavra chave inválida');
		$categoryNew = $request->getBit('category_new');

		$request->getUnique('name', 'so_categories');

		if($request->checkError()) {
				
			$model->name = $name;
			$model->category_order = $categoryOrder;
			$model->category_id = $categoryId;
			$model->status = $status;
			$model->type = $type;
			$model->color = $color;
			$model->category_show = $categoryShow;
			$model->description = $description;
			$model->keywords = $keywords;
			$model->category_new = $categoryNew;

			if($model->save()) {
				return alert("Categoria {$name} criado com sucesso", "success");
			} else {
				return alert("Erro ao criar a categoria", "error");
			}
		}else {
			return alert($request->getErrorMessage(), "error");
		}
		
	}

	/*Retorna os tipos de categorias*/
	public function getTypes($type=null)
	{
		$types = array(
			1 =>'Produto',
			2 =>'Serviços',
			3 =>'Cursos',
			4 =>'Página',
			5 =>'Post',
			6 =>'Faq'
		);
		return !is_null($type) && array_key_exists($type,$types) ? $types[$type]:$types;
	}

	public function destroy()
	{
	}
}
