<?php

namespace Q8Intouch\Q8Query\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Q8Intouch\Q8Query\Query;

class QueryController extends BaseController
{
    public function get(Request $request, $url)
    {
//        User::getA();
        return Query::QueryFromPathString($url)->build();
//        return User::whereKey(1)->first()->order()->whereKey(1)->first()->coupon()->whereKey(1)->first();
//        return $url;
    }
}