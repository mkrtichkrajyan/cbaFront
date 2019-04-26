<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Cookie\Factory as CookieFactory;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;

if (!function_exists('backend_asset_path')) {
    /**
     * Generate an backend_asset path for the application.
     *
     * @param  string $path
     * @param  bool $secure
     * @return string
     */
    function backend_asset_path($path = null)
    {
        return env('BACKEND_URL') . $path . '/';
    }
}

if (!function_exists('backend_asset')) {
    /**
     * Generate an backend_asset path for the application.
     *
     * @param  string $path
     * @param  bool $secure
     * @return string
     */
    function backend_asset($path = null)
    {
        return config('app.BACKEND_URL') . '/' . $path;
//        return env('BACKEND_URL') . '/'. $path ;
    }
}
