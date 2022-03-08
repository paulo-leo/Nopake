<?php
namespace Modules\Book\Models;

use Nopadi\MVC\Model;

class BookModel extends Model
    {
      protected $table = 'np_books';

	  /*Prover o acesso estático ao modelo*/
	  public static function model()
	  {
		return new BookModel();
	  } 	
    }