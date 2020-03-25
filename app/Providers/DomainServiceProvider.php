<?php

namespace App\Providers;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\EloquentUserRepository;
use App\Domain\User\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class DomainServiceProvider
 * @package App\Providers
 */
class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepositories();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register repositories
     */
    protected function registerRepositories()
    {
        $this->app->singleton(UserRepository::class, function ($app) {
            return new EloquentUserRepository(new User(), $app['db']);
        });
    }
}
