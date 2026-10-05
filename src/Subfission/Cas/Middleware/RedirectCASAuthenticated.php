<?php

namespace Subfission\Cas\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class RedirectCASAuthenticated
{
    protected $auth;
    protected $cas;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->cas = resolve('cas');
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->cas->checkAuthentication()) {
            return redirect(config('cas.cas_redirect_path'));
        }

        return $next($request);
    }
}
