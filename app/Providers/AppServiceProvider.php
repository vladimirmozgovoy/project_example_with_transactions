<?php

namespace App\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(AccessTokenController::class, \App\Http\Controllers\API\v1\AccessTokenController::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // устанавливаем максимальную длину для varchar-полей при создании миграции
        Builder::defaultStringLength(191);
        Validator::extend('array_of_integer', function($attribute, $value, $parameters)
        {
            foreach($value as $v) {
                if(!intval($v)) return false;
            }
            return true;
        });

    }
}
