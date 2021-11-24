<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ConnectionDB;

class customAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->token;
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $data = $collection->findOne(['remember_token'=>$token]);
        if($data!=NULL)
        {
            return $next($request);
        }
        else
        {
            return response("Your are not Authenticated User. / Token Not Matched in Middleware.");
        }
    }
}
