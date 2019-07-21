<?php

use Illuminate\Support\Facades\Route;
use Q8Intouch\Q8Query\Http\Controllers\QueryController;

Route::get('/{resource}', QueryController::class.'@get')
    ->where('resource', '.*');;