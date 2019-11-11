<?php

namespace TM\Crud;

use Illuminate\Support\ServiceProvider;

class CRUDServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        include __DIR__.'/routes.php';



        $this->app->bind('\Illuminate\Routing\ResourceRegistrar', function (){
            dd(123);
            $registrar = new ResourceRegistrar($this->app['router']);
            return $registrar;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->make('TM\Crud\CRUDController');
        $this->app->make('TM\Crud\CRUDModel');
        $this->loadViewsFrom(__DIR__.'/views', 'tm');
    }
}
