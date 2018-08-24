<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;

use Closure;

class CheckRole
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
        // Si el suaruio no esta loggeado, se trata no tiene permisos

        if($request->user() === null){
          return response("Permisos Insuficientes", 401);
        }

        // Si el usuario esta loggeado, se obtiene su vigencia (activo)
        $activo = $request->user()->is_active;

        // Se obtienen sus roles
        $actions = $request->route()->getAction();
        $roles = isset($actions['roles']) ? $actions['roles'] : null;

        if (($request->user()->hasAnyRole($roles) || !$roles) and $activo){
            return $next($request);
        }
        return response("Permisos Insuficientes", 401);
    }
}
