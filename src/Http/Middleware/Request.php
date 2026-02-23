<?php

namespace Appstract\Opcache\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Request
{
    public function handle($request, Closure $next)
    {
        if (! $this->isAllowed($request)) {
            throw new HttpException(403, 'This action is unauthorized.');
        }

        return $next($request);
    }

    protected function isAllowed($request): bool
    {
        try {
            $decrypted = Crypt::decrypt($request->get('key'));
        } catch (DecryptException $e) {
            $decrypted = '';
        }

        // $request->ip() respects Laravel's TrustProxies middleware,
        // unlike reading $_SERVER headers directly.
        return $decrypted === 'opcache'
            || in_array($request->ip(), [$this->getServerIp(), '127.0.0.1', '::1']);
    }

    protected function getServerIp(): string
    {
        return $_SERVER['SERVER_ADDR'] ?? $_SERVER['LOCAL_ADDR'] ?? '127.0.0.1';
    }
}
