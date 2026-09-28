<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmailVerification
{
public function handle($request, Closure $next)
{
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login.form');
    }

    // bypass admin
    if (in_array($user->role, ['admin', 'super_admin'])) {
        return $next($request);
    }

    // if (!$user->hasVerifiedEmail()) 
    if (!$user->email_verified_at){
        return redirect()->route('verification.notice');
    }

    return $next($request);
}
}