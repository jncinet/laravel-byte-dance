<?php

namespace Jncinet\LaravelByteDance;

use Illuminate\Support\ServiceProvider;

class ByteDanceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('byte-dance.dou-yin', function () {
            return ByteDance::DouYin();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/byte-dance.php' => config_path('byte-dance.php')
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/byte-dance.php', 'byte-dance');
    }
}