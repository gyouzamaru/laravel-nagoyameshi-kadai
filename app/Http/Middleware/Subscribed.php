<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Subscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        if(! $request->user()?->subscribed('premium_plan')) {
            return redirect('subscription/create');
        }
        return $next($request);
    }
}
