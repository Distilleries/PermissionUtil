<?php namespace Distilleries\PermissionUtil\Http\Middleware;

use Closure;
use Distilleries\PermissionUtil\Helpers\PermissionUtil;

class CheckAccessPermission {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $permission;

	/**
	 * Create a new filter instance.
	 *
	 * @return void
	 */
	public function __construct(PermissionUtil $permission)
	{
		$this->permission = $permission;
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
