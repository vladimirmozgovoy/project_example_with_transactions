<?php
/**
 * Created by PhpStorm.
 * User: XpoHo
 * Date: 24.10.2019
 * Time: 12:40
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        if ( $request->getMethod() === "OPTIONS" ) {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', '*');
        }

        if ( in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])
            && $request->isJson()
        ) {

            $data = $request->json()->all();
            $request->request->replace(is_array($data) ? $data : []);
        }

        if(1 == 0){
            return response()
                ->json($request->post(), 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', '*');

        }


        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Headers', '*')
            ->header('Access-Control-Allow-Methods', '*');
    }
}
