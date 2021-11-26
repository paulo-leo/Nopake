<?php

namespace Modules\Ecommerce;

use Nopadi\Http\Param;
use Nopadi\Http\Route;
use Nopadi\MVC\Module;
use Nopadi\Http\Request;

class Ecommerce extends Module
{
	public function main()
	{
		Route::resources('ecommerce/categories', '@Ecommerce/Controllers/CategoryController');

		Route::resources('ecommerce/products', '@Ecommerce/Controllers/ProductController');
		
		Route::resources('ecommerce/faqs', '@Ecommerce/Controllers/FaqController');
	
	}
}
