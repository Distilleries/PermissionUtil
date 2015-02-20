<?php namespace Distilleries\FormBuilder\Helpers;

use Distilleries\PermissionUtil\Contracts\PermissionUtilContract;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\SessionInterface;

class PermissionUtil implements PermissionUtilContract {

    protected $auth;
    protected $config;
    protected $session;

    public function __construct(Guard $auth, SessionInterface $session, array $config = []) {
        $this->auth   = $auth;
        $this->config = $config;
        $this->session = $session;
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

}