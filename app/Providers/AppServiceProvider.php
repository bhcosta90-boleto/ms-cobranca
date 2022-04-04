<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Str::macro('truncate', function ($value, $precision = 2) {
            return intval($value * ($p = pow(10, $precision))) / $p;
        });
    }
}
