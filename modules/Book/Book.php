<?php 
namespace Modules\Book; 

use Nopadi\MVC\Module;
use Nopadi\Http\Route;
use Nopadi\Base\Table;


class Book extends Module
{
	/*Este é método principal para execução do módulo enquanto ativo*/
	public function main()
    {
		 $books = array(
           'book'=>'index'
		 );

         Route::controllers($books,'@Book/Controllers/DocumentationController');
		 Route::resources('dashboard/books','@Book/Controllers/BookController');

		 Route::get('dashboard/books/teste',function(){

          $arr = array(
			  'name'=>'Paulo',
			  'phone'=>'54154'
		  );

		  json($arr);

		 });
        


    }

	/*Ativa o módulo*/
	public function on()
	{
          $table = new Table;

		  $table->create(array(
			     'np_books'=>array('id|pk',
				  'code|string|size:20',
				  'name|string|size:80',
				  'description|string|size:250',
				  'status|number|size:2|default:1',
				  'privacy|number|size:1|default:1',
				  'created_at|timestamp'),
				  'np_chapters'=>array('id|pk',
				  'name|string|size:80',
				  'description|string|size:250',
				  'number|number|size:3|default:1',
				  'book_id:np_books:id|fk',
				  'created_at|timestamp'),
				  'np_versions'=>array('id|pk',
				  'name|string|size:40',
				  'type|number|size:1|default:1',
				  'description|string|size:125',
				  'status|number|size:1|default:1',
				  'book_id:np_books:id|fk',
				  'created_at|timestamp'),
				  'np_pages'=>array('id|pk',
				  'name|string|size:40',
				  'type|number|size:1|default:1',
				  'privacy|number|size:1|default:1',
				  'description|string|size:125',
				  'content|longtext|null',
				  'status|number|size:1|default:1',
				  'chapter_id:np_chapters:id|fk',
				  'created_at|timestamp',
				  'updated_at|datetime|null'),
				  'np_list_versions'=>array('id|pk',
				  'page_id:np_pages:id|fk',
				  'version_id:np_versions:id|fk'),
				  'np_notes'=>array('id|pk',
				  'type|number|size:1|default:1',
				  'sub_type|number|size:1|default:1',
				  'content|text',
				  'page_id:np_pages:id|fk',
				  'created_at|timestamp',
				  'updated_at|datetime|null')
		  ));
	}

	/*Desativa o módulo*/
	public function off()
	{
          $table = new Table();

		  $table->drop([
		  'np_list_versions',
		  'np_notes',
		  'np_pages',
		  'np_versions',
		  'np_chapters',
		  'np_books']);
	}
} 
