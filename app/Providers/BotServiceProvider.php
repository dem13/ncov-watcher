<?php

namespace App\Providers;

use App\BotKernel\Bot;
use App\BotKernel\Handlers\Feedback;
use App\BotKernel\Handlers\SetCategory;
use App\BotKernel\Handlers\SetContact;
use App\BotKernel\Handlers\SetName;
use App\BotKernel\Handlers\SetPhoto;
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

        Log::debug('here');

        $this->app->singleton(Bot::class, function () {
            return new Bot();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $bot = $this->app->make(Bot::class);
        $bot
            ->addHandler(Start::class, '/start');

        Log::info('Bot is configured');

    }
}
