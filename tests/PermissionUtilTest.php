<?php
/**
 * Created by PhpStorm.
 * User: cross
 * Date: 2/24/2015
 * Time: 11:46 AM
 */

use \Mockery as m;

class PermissionUtilTest extends PHPUnit_Framework_TestCase {


    protected $guard;
    protected $auth;

    public function setUp(){

        $this->guard = m::mock('Illuminate\Contracts\Auth\Guard');
        $this->auth  = m::mock('Illuminate\Contracts\Auth\Authenticatable');
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetPermissionsAuthenticated()
    {
        $this->guard->shouldReceive('user')->andReturn($this->auth);
        $this->guard->shouldReceive('check')->once()->andReturn(true);
        $perm = new \Distilleries\PermissionUtil\Helpers\PermissionUtil($this->guard, ['auth_restricted' => true]);
        $this->assertTrue($perm->hasAccess("test"));
    }

    public function testGetPermissionsDeniedNotAuthenticated()
    {
        $this->guard->shouldReceive('user')->andReturn($this->auth);
        $this->guard->shouldReceive('check')->once()->andReturn(false);

        $perm = new \Distilleries\PermissionUtil\Helpers\PermissionUtil($this->guard, ['auth_restricted' => true]);

        $this->assertFalse($perm->hasAccess("test"));
    }

    public function testGetPermissionsAuthenticatedWithImplement()
    {
        $this->guard->shouldReceive('user')->andReturn($this->auth);
        $this->guard->shouldReceive('check')->once()->andReturn(true);

        $this->auth->shouldReceive('hasAccess')->andReturn(true);

        $perm = new \Distilleries\PermissionUtil\Helpers\PermissionUtil($this->guard, ['auth_restricted' => true]);

        $this->assertTrue($perm->hasAccess("test"));
    }

    public function testGetPermissionsDeniedAuthenticatedWithImplement()
    {
        $this->guard->shouldReceive('user')->andReturn($this->auth);
        $this->guard->shouldReceive('check')->once()->andReturn(false);

        $this->auth->shouldReceive('hasAccess')->andReturn(false);

        $perm = new \Distilleries\PermissionUtil\Helpers\PermissionUtil($this->guard, ['auth_restricted' => true]);

        $this->assertFalse($perm->hasAccess("test"));
    }

    public function testMiddlewareCheckAccessPermission()
    {

        $perm = m::mock('Distilleries\PermissionUtil\Contracts\PermissionUtil');
        $perm->shouldReceive('hasAccess')->andReturnUsing(function ($slug)
        {
            return $slug == 'test';
        });

        $tr = m::mock();
        $tr->shouldReceive('trans')->andReturn('');

        $app = m::mock('Illuminate\Contracts\Container\Container');
        $app->shouldReceive('make')->with('permission-util')->andReturn($perm);
        $app->shouldReceive('make')->with('translator')->andReturn($tr);
        $app->shouldReceive('abort')->andThrow(new \Exception());

        $check = new \Distilleries\PermissionUtil\Http\Middleware\CheckAccessPermission($app);

        $route = m::mock();
        $route->shouldReceive('getActionName')->andReturn('test');

        $http = m::mock('Illuminate\Http\Request');
        $http->shouldReceive('route')->andReturn($route);

        $this->assertEquals(1, $check->handle($http, function ()
        {
            return 1;
        }));

        $route = m::mock();
        $route->shouldReceive('getActionName')->andReturn('yest');

        $http = m::mock('Illuminate\Http\Request');
        $http->shouldReceive('route')->andReturn($route);
        try
        {
            $check->handle($http, function ()
            {
                return 1;
            });
        } catch (\Exception $expected)
        {
            return;
        }


        $this->fail('Exception');

    }

}