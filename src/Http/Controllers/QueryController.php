<?php

namespace Q8Intouch\Q8Query\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Q8Intouch\Q8Query\Core\ModelNotFoundException;
use Q8Intouch\Q8Query\Core\ParamsMalformedException;
use Q8Intouch\Q8Query\Filterer\NoStringMatchesFound;
use Q8Intouch\Q8Query\Query;

class QueryController extends BaseController
{
    public function get(Request $request, $url)
    {
//        return User::whereHas('order.address')->get();
        try {
            return Query::QueryFromPathString($url)->build();
        } catch (\Exception $e) {
            dd($e);
        }
//        return User::whereKey(1)->first()->order()->whereKey(1)->first()->coupon()->whereKey(1)->first();
//        return $url;
    }
}