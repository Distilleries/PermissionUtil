<?php namespace Distilleries\PermissionUtil;

use Distilleries\PermissionUtil\Helpers\PermissionUtil;
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
        $this->app->singleton('permission-util', function($app) {
            return new PermissionUtil($app->make('Illuminate\Contracts\Auth\Guard'), $app['config']->get($this->package));
        });
    }

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang/', $this->package);
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