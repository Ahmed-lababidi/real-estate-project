<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DocsBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $username = env('DOCS_USERNAME');
        $password = env('DOCS_PASSWORD');

        $inputUser = $request->getUser();
        $inputPass = $request->getPassword();

        if ($inputUser !== $username || $inputPass !== $password) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="API Documentation"',
            ]);
        }

        return $next($request);
    }
}
