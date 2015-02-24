<?php namespace Distilleries\PermissionUtil\Http\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class CheckAccessPermission {

    protected $app;
    /**
     * The Guard implementation.
     *
     * @var \Distilleries\PermissionUtil\Helpers\PermissionUtil
     */
    protected $permission;

    /**
     * Create a new filter instance.
     *
     */
    public function __construct(Container $app)
    {
        $this->app        = $app;
        $this->permission = $app->make('permission-util');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (!$this->permission->hasAccess($request->route()->getActionName()))
        {
            $this->app->abort(403, $this->app->make('translator')->trans('permission-util::errors.unthorized'));
        }

        return $next($request);
    }
}