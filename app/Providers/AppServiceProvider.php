<?php

namespace App\Providers;

use App\Ncov\Crawler\ICrawler;
use App\Ncov\Crawler\WikipediaCrawler;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
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

        $this->app->bind(Filesystem::class, function () {
            return Storage::disk('public');
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
