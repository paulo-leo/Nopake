<?php

namespace App\Providers;

use Nopadi\Http\Auth;
use Nopadi\Http\Param;
use Nopadi\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if(Param::getIndex(0,'dashboard'))
		{
			if(!Auth::check())
			{
				 to_url('login');
			}
		}
    }
}
