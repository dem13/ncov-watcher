<?php

namespace App\Providers;

use App\Ncov\Crawler\ICrawler;
use App\Ncov\Crawler\WikipediaCrawler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ICrawler::class, function () {
            return new WikipediaCrawler();
        });
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
}
