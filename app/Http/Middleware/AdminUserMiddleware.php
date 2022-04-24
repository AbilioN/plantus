<?php

namespace App\Http\Middleware;

use App\Exceptions\RoleIdNotAllowedException;
use App\Models\UserRoles;
use Tymon\JWTAuth\Facades\JWTAuth;

use Closure;

class AdminUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();

            $userRole = UserRoles::where('user_id', $user->id)->first();
            if($userRole->id !== 1)
            {
                throw new RoleIdNotAllowedException();
            }

        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status' => 'Token is Invalid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'Token is Expired']);
            }
            else if($e instanceof RoleIdNotAllowedException)
            {
                return response()->json(['status' => 'Usuário não tem permissão para acessar esta rota']);

            }
            else{
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }
        return $next($request);
    }
}
