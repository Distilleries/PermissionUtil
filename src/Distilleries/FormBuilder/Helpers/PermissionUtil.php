<?php namespace Distilleries\FormBuilder\Helpers;

use Distilleries\PermissionUtil\Contracts\PermissionUtilContract;
use Illuminate\Auth\AuthManager;
use Illuminate\Session\SessionManager;

class PermissionUtil implements PermissionUtilContract {

    protected $auth;
    protected $config;
    protected $session;

    public function __construct(AuthManager $auth, SessionManager $session, array $config = []) {
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

            $user = $this->auth->get();
            $implement = class_implements($user,true);

            if (empty($implement) || empty($implement['Distilleries\PermissionUtil\Contracts\PermissionUtilContract'])) {
                return true;
            }

            return (!empty($user)) ? $user->hasAccess($key) : false;
        }

        return false;
    }

}