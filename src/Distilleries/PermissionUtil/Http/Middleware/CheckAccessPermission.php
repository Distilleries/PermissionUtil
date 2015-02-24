<?php namespace Distilleries\PermissionUtil\Http\Middleware;

use Closure;

class CheckAccessPermission {

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
	public function __construct()
	{
		$this->permission = app('permission-util');
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		if (!$this->permission->hasAccess($request->route()->getActionName())) {
			abort(403, trans('permission-util::errors.unthorized'));
		}

		return $next($request);
	}
}
