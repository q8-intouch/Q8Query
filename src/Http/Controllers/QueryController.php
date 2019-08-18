<?php

namespace Q8Intouch\Q8Query\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Q8Intouch\Q8Query\Core\ModelNotFoundException;
use Q8Intouch\Q8Query\OptionsReader\OptionsReader;
use Q8Intouch\Q8Query\Query;

class QueryController extends BaseController
{
    public function get(Request $request, $url)
    {
        try {
            return Query::QueryFromPathString($url)->get();
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function options(Request $request, $resource)
    {
        try {
            return OptionsReader::createFromModelString($resource)->extractOptions();
        } catch (ModelNotFoundException $e) {
            dd($e);
        }
    }
}