<?php
/**
 * Created by PhpStorm.
 * User: cross
 * Date: 2/25/2015
 * Time: 11:07 AM
 */

use \Mockery as m;

class ServiceTest extends PHPUnit_Framework_TestCase{


    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testService(){

        $app = m::mock('\Illuminate\Contracts\Foundation\Application');

        $result = new \Distilleries\PermissionUtil\PermissionUtilServiceProvider($app);

        $facades = $result->provides();
        $fac = \Distilleries\PermissionUtil\Facades\PermissionUtil::getFacadeAccessor();

        $this->assertTrue(in_array($fac, $facades));
    }
} 