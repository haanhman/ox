<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Repositories\GroupRepository::class, \App\Repositories\GroupRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WordRepository::class, \App\Repositories\WordRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\YoutubeRepository::class, \App\Repositories\YoutubeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WordTypeRepository::class, \App\Repositories\WordTypeRepositoryEloquent::class);
        //:end-bindings:
    }
}
