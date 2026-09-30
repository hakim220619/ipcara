<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Saat aplikasi dibagikan lewat tunnel (ngrok), browser yang pernah menerima
 * redirect 301 lama (mis. ke ipcara.test) menyimpannya secara permanen.
 * Header Clear-Site-Data meminta browser mengosongkan cache domain tersebut,
 * sekali per browser (ditandai cookie), sehingga redirect lama ikut terhapus.
 */
class ClearStaleBrowserCache
{
    private const COOKIE = 'ipcara_cache_reset';

    private const TUNNEL_HOSTS = ['.ngrok-free.dev', '.ngrok-free.app', '.ngrok.app', '.ngrok.io'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->isTunnel($request->getHost()) || $request->cookies->has(self::COOKIE)) {
            return $response;
        }

        $response->headers->set('Clear-Site-Data', '"cache"');
        $response->headers->setCookie(Cookie::create(self::COOKIE, '1', now()->addYear(), '/', null, $request->isSecure(), true, false, 'lax'));

        return $response;
    }

    private function isTunnel(string $host): bool
    {
        foreach (self::TUNNEL_HOSTS as $suffix) {
            if (str_ends_with($host, $suffix)) {
                return true;
            }
        }

        return false;
    }
}
