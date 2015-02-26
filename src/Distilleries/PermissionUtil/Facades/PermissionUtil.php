<?php namespace Distilleries\PermissionUtil\Facades;

use Illuminate\Support\Facades\Facade;

class PermissionUtil extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'permission-util';
    }
}
