<?php

namespace App\Providers;

use App\BotKernel\Bot;
use App\BotKernel\Handlers\Start;
use App\BotKernel\User\EloquentUserManager;
use App\BotKernel\User\IBotUserManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class BotServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(IBotUserManager::class, function () {
            return new EloquentUserManager();
        });

        $this->app->singleton(Bot::class, function () {
            return new Bot();
        });
    }

    /**
     * Bootstrap services.
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        $bot = $this->app->make(Bot::class);
        $bot
            ->addHandler(Start::class, '/start');

        Log::info('Bot is configured');

    }
}