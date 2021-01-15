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
                __DIR__ . '/../config/byte-dance.php' => config_path('byte-dance.php'),
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/byte-dance'),
            ]);
        }
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'byte-dance');
        $this->mergeConfigFrom(__DIR__ . '/../config/byte-dance.php', 'byte-dance');
    }
}