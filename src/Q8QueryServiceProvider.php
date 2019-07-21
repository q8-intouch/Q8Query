<?php

namespace Q8Intouch\Q8Query;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Q8QueryServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Route::prefix(config('url_prefix', 'Q8Query'))->group(__DIR__.'/../routes/api.php');

        $this->publishes([
            __DIR__ . '/../config/q8-query.php' => config_path('q8-query.php'),
        ], 'config');
    }
    public function register()
    {

    }

}