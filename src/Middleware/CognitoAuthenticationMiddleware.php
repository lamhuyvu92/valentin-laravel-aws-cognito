<?php
namespace pmill\LaravelAwsCognito\Middleware;

use pmill\LaravelAwsCognito\ApiGuard;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CognitoAuthenticationMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next)
    {
        /** @var ApiGuard $guard */
        $guard = Auth::guard('aws-cognito');

        if (!$guard->validateToken()) {
            throw new UnauthorizedHttpException('invalid_token');
        }

        /** @var Response $response */
        return $next($request);
    }
}