<?php
/**
 * Created by PhpStorm.
 * User: cross
 * Date: 2/24/2015
 * Time: 11:46 AM
 */

use \Mockery as m;

class PermissionUtilTest extends \Orchestra\Testbench\TestCase {


    protected $guard;
    protected $auth;

    public function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders()
    {
        return [ 'Distilleries\PermissionUtil\PermissionUtilServiceProvider' ];
    }

    protected function getPackageAliases()
    {
        return [
            'Perm' => 'Distilleries\PermissionUtil\Facades\PermissionUtil'
        ];
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetPermissionsNoConfig()
    {
        $this->refreshApplication();
        $this->app['config']->set('permission-util', []);
        $this->assertTrue(Perm::hasAccess(null));
    }

    public function testGetPermissionsDeniedNotAuthenticated()
    {
        $this->refreshApplication();
        $this->app['config']->set('permission-util', ['auth_restricted' => true]);
        $this->assertFalse(Perm::hasAccess("test"));
    }

    public function testGetPermissionsAuthenticated()
    {
        $this->refreshApplication();
        $user = new User(['name' => 'John']);
        $this->be($user);
        $this->app['config']->set('permission-util', ['auth_restricted' => true]);
        $this->assertTrue(Perm::hasAccess("diff"));
    }

    public function testGetPermissionsAuthenticatedWithImplement()
    {
        $this->refreshApplication();
        $user = new UserImplement(['name' => 'John']);
        $this->be($user);
        $this->app['config']->set('permission-util', ['auth_restricted' => true]);

        $this->assertTrue(Perm::hasAccess("test"));
    }

    public function testGetPermissionsDeniedAuthenticatedWithImplement()
    {
        $this->refreshApplication();
        $user = new UserImplement(['name' => 'John']);
        $this->be($user);
        $this->app['config']->set('permission-util', ['auth_restricted' => true]);

        $this->assertFalse(Perm::hasAccess("diff"));
    }

    public function testMiddlewareCheckAccessPermission()
    {
        $this->refreshApplication();
        $user = new UserImplement(['name' => 'John']);
        $this->be($user);

        $check = new \Distilleries\PermissionUtil\Http\Middleware\CheckAccessPermission($this->app);

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


use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use \Distilleries\PermissionUtil\Contracts\PermissionUtilContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

}

class UserImplement extends Model implements AuthenticatableContract, CanResetPasswordContract, PermissionUtilContract {

    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function hasAccess($key){
        return $key == "test";
    }
}


