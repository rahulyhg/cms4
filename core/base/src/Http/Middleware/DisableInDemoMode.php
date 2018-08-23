<?php

namespace Botble\Base\Http\Middleware;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Closure;

class DisableInDemoMode
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @author Sang Nguyen
     * @since 2.1
     */
    public function handle($request, Closure $next)
    {
        if (app()->environment() == 'demo') {
            return (new BaseHttpResponse())
                ->setError(true)
                ->withInput(true)
                ->setMessage(trans('core.base::system.disabled_in_demo_mode'))
                ->toResponse($request);
        }

        return $next($request);
    }
}
