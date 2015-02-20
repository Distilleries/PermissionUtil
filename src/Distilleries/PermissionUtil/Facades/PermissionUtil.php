<?php namespace Distilleries\PermissionUtil\Facades;

use Illuminate\Support\Facades\Facade;

class PermissionUtil extends Facade {

    public static function getFacadeAccessor()
    {
        return 'permission-util';
    }
}
