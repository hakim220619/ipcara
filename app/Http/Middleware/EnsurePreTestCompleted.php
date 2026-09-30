<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsurePreTestCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->needsPreTest()) {
            Inertia::flash('toast', ['type' => 'info', 'message' => 'Kerjakan pre-test terlebih dahulu sebelum mulai belajar.']);

            return to_route('pre-test.index');
        }

        return $next($request);
    }
}
