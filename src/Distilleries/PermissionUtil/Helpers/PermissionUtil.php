<?php namespace Distilleries\PermissionUtil\Helpers;

use Distilleries\PermissionUtil\Contracts\PermissionUtilContract;
use Illuminate\Contracts\Auth\Guard;

class PermissionUtil implements PermissionUtilContract {

    protected $auth;
    protected $config;

    public function __construct(Guard $auth, array $config = []) {
        $this->auth   = $auth;
        $this->config = $config;
    }

    public function hasAccess($key)
    {
        if (empty($this->config['auth_restricted'])) {
            return true;
        }

        if ($this->auth->check()) {

            $user = $this->auth->user();
            $implement = class_implements($user, true);

            if (empty($implement) || empty($implement['Distilleries\PermissionUtil\Contracts\PermissionUtilContract'])) {
                return true;
            }

            return (!empty($user)) ? $user->hasAccess($key) : false;
        }

        return false;
    }

    /*
     * Checks each elements of the array if the access is granted
     * @param  Array $arrayKeys
     * @param  Boolean $isAndRelation: if true its a AND relation check, if false a OR relation
     * @param  String $child: if set, the hasAccess method will be called using element[$child] instead of the array's element itself
     * @return boolean
     */
    public function hasAccessArray($arrayKeys, $isAndRelation = false, $child = null) {
        $hasAccess = null;
        foreach ($arrayKeys as $key) {
            if ($hasAccess === null) {
                $hasAccess = $this->hasAccess(($child == null ? $key : $key[$child]));
            } else {
                if ($isAndRelation) {
                    $hasAccess = $hasAccess && $this->hasAccess(($child == null ? $key : $key[$child]));
                    if (!$hasAccess) break;
                } else {
                    $hasAccess = $hasAccess || $this->hasAccess(($child == null ? $key : $key[$child]));
                }
            }
        }
        return $hasAccess != null ?: false;
    }
}