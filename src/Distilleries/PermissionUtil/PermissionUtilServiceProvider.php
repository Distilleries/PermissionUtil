<?php namespace Distilleries\PermissionUtil;

use Distilleries\FormBuilder\Helpers\PermissionUtil;
use Illuminate\Support\ServiceProvider;

class PermissionUtilServiceProvider extends ServiceProvider {


    protected $package = 'permission-util';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php',
            $this->package
        );

        $this->registerPermissionUtils();


    }

    protected function registerPermissionUtils()
    {
        $this->app->bindShared('permission-util', function($app) {
            return new PermissionUtil($app['auth'], $app['session'], $app['config']->get($this->package));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path($this->package.'.php')
        ]);
    }


    /**
     * @return string[]
     */
    public function provides()
    {
        return ['permission-util'];
    }
}