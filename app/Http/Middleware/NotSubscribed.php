<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        if($request->user()?->subscribed('premium_plan')){
            return redirect('subscription/edit');
        }
        return $next($request);
    }
}
