<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToRolePanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $rolePanelMap = [
                'subbagrenmin' => '/subbagrenmin',
                'sikorwas'     => '/sikorwas',
                'bagwassidik'  => '/bagwassidik',
                'bagbinopsnal' => '/bagbinopsnal',
                'admin'        => '/admin',
                // 'super_admin'        => '/admin',
            ];

            $userRoles = $user->getRoleNames()->map(fn($r) => Str::lower($r))->toArray();

            foreach ($rolePanelMap as $role => $panelUrl) {
                if (in_array($role, $userRoles) && $request->is('admin')) {
                    return redirect($panelUrl);
                }
            }
        }

        return $next($request);
    }


}
