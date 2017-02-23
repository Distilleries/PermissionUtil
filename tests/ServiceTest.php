<?php
/**
 * Created by PhpStorm.
 * User: cross
 * Date: 2/25/2015
 * Time: 11:07 AM
 */

use \Mockery as m;

class ServiceTest extends \Orchestra\Testbench\BrowserKit\TestCase
{


    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    protected function getPackageProviders()
    {
        return ['Distilleries\PermissionUtil\PermissionUtilServiceProvider'];
    }

    protected function getPackageAliases()
    {
        return [
            'Perm' => 'Distilleries\PermissionUtil\Facades\PermissionUtil'
        ];
    }

    public function testService()
    {

        $service = $this->app->getProvider('Distilleries\PermissionUtil\PermissionUtilServiceProvider');
        $facades = $service->provides();
        $this->assertTrue(['permission-util'] == $facades);

        $service->boot();
        $service->register();
    }
} 