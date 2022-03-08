<?php
namespace Modules\Book\Controllers; 

use Nopadi\MVC\Controller;
use Nopadi\Http\Request;
use Modules\Book\Models\BookModel; 

class BookController extends Controller
{
    public function create()
    {
        
       return view('@Book/Views/index');
    }


    public function index()
    {
        $model = new BookModel;

        $model = $model->ipaginate(10,true);

        json($model);

    }

    public function store()
    {
      $request = new Request;

      $code = date('simymd');
      $name = $request->getString('name','5:80');
      $description = $request->getString('description','5:250');
      $privacy = $request->get('privacy',1);

      $request->getUnique('name','np_books');

      $model = new BookModel;

      if($request->checkMessages()){
        $model->insert([
            'name'=>$name,
            'description'=>$description,
            'privacy'=>$privacy,
            'status'=>1,
            'code'=>$code
        ]);
      }else{
         var_dump($request->getMessages());
      }
    }
} 