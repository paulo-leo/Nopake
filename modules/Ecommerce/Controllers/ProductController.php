<?php

namespace Modules\Ecommerce\Controllers;

use Nopadi\Http\Send;
use Nopadi\Http\Param;
use Nopadi\Http\Request;
use Nopadi\MVC\Controller;
use Modules\Ecommerce\Models\ProductModel;


class ProductController extends Controller
{
	/*Exibe a página principal*/
	public function index()
	{
		$product = new ProductModel;
		$product = $product->as('p')
		->select(['p.*','c.name as category_name'])
		->leftJoin('so_categories c','c.id','p.category_id')
		->paginate();			

		return view('@Ecommerce/Views/products/list', ['product' => $product]);
	}

	/*Exibe a página para edição*/
	public function edit()
	{
		$id = Param::int('id');

		if ($id) {
			$model = new ProductModel;
			$data = $model->find($id);

			return view("@Ecommerce/Views/products/edit", ['data' => $data, 'types' => $this->getTypes()]);
		}
	}

	/*Processa os dados vindos da página de edição*/
	public function update()
	{

		$model = new ProductModel;
		$request = new Request();

		$request->getUnique('code', 'so_products');

		$requestId = $request->getInt('id');
		$productNew = $request->getInt('product_new');
		$productOrder = $request->getInt('product_order');
		$productShow = $request->getInt('product_show');
		$barcode = $request->getString('barcode', '1:20', ' - Código de barras inválido');
		$code = $request->getString('code', '1:20', ' - Código inválido');
		$name = $request->getString('name', '1:100', ' - Nome inválido');
		$description = $request->getString('description', '1:250', ' - Descrição inválida');
		$price = $request->getFloat('price');
		$promotionalPrice = $request->getFloat('promotional_price');
		$keywords = $request->getString('keywords', '1:250', ' - Palavra chave inválida');
		$quantity = $request->getInt('quantity');
		$stock = $request->getInt('stock');
		$onDate = $request->getDate('on_date');
		$offDate = $request->getDate('off_date');	
		$status = $request->getInt('productStatus');
		$category_id = $request->getInt('category_id');
		$type = $request->getInt('type');

		if($request->checkError())
		{
			$model->type = $type;
			$model->code = $code;
			$model->barcode = $barcode;
			$model->product_order = $productOrder;
			$model->product_show = $productShow;
			$model->status = $status;
			$model->product_new = $productNew;
			$model->name = $name;
			$model->description = $description;
			$model->price = $price;
			$model->promotional_price = $promotionalPrice;
			$model->keywords = $keywords;
			$model->quantity = $quantity;
			$model->stock = $stock;
			$model->on_date = $onDate;
			$model->off_date = $offDate;
			$model->status = $status;
			$model->category_id = $category_id;

			if($model->update($model, $requestId)) {
				return alert('Dados alterados com sucesso', 'success');
			} else {
				return alert('Não foi possivel atualizar os dados', 'error');
			}
		} else {
			return alert($request->getErrorMessage(), 'error');
		}	
	}

	/*Exibe a página de criação de produto*/
	public function create()
	{
		$data = array(
			'types' => $this->getTypes()
		);
		return view("@Ecommerce/Views/products/create", $data);
	}

	/*Salva os dados vindos da página de criação*/
	public function store()
	{
		// FALTA IMAGEM
		$request = new Request();

		$request->getUnique('code', 'so_products');

			$productNew = $request->getInt('product_new');
			$productOrder = $request->getInt('product_order');
			$productShow = $request->getInt('product_show');
			$barcode = $request->getString('barcode', '1:20', ' - Código de barras inválido');
			$code = $request->getString('code', '1:20', ' - Código inválido');
			$name = $request->getString('name', '1:100', ' - Nome inválido');
			$description = $request->getString('description', '1:250', ' - Descrição inválida');
			$price = $request->getFloat('price');
			$promotionalPrice = $request->getFloat('promotional_price');
			$keywords = $request->getString('keywords', '1:250', ' - Palavra chave inválida');
			$quantity = $request->getInt('quantity');
			$stock = $request->getInt('stock');
			$onDate = $request->getDate('on_date');
			$offDate = $request->getDate('off_date');	
			$status = $request->getInt('productStatus');
			$category_id = $request->getInt('category_id');
			$type = $request->getInt('type');

			$model = new ProductModel;

			if($request->checkError()) {

				$model->type = $type;
				$model->code = $code;
				$model->barcode = $barcode;
				$model->product_order = $productOrder;
				$model->product_show = $productShow;
				$model->status = $status;
				$model->product_new = $productNew;
				$model->name = $name;
				$model->description = $description;
				$model->price = $price;
				$model->promotional_price = $promotionalPrice;
				$model->keywords = $keywords;
				$model->quantity = $quantity;
				$model->stock = $stock;
				$model->on_date = $onDate;
				$model->off_date = $offDate;
				$model->category_id = $category_id;
				$model->type = $type;		

			if ($model->insert($model)) {
				return alert("Produto {$name} com id {$category_id} criada com sucesso", "success");
			} else {
				return alert("Erro ao adicionar produto.", "error");
			}
		} else {
			return alert($request->getErrorMessage(), 'error');
		}
	}

	/*Retorna os tipos de categorias*/
	public function getTypes()
	{
		$types = array(
			1 => 'Produto',
			2 => 'Cursos', 
			3 => 'Serviços'
		);
		return $types;
	}
}
