<?php

namespace App\Providers;

use App\BotKernel\Bot;
use App\BotKernel\Handlers\Chart;
use App\BotKernel\Handlers\NcovInfo;
use App\BotKernel\Handlers\Start;
use App\BotKernel\MessengerContexts\IMessengerContext;
use App\BotKernel\User\EloquentUserManager;
use App\BotKernel\User\IBotUserManager;
use App\Telegram\Repositories\ChatRepository;
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
            ->addHandler(Start::class, fn (IMessengerContext $messenger) => strpos($messenger->getMessage(), '/start') === 0 || strpos($messenger->getMessage(), '/help') === 0)
            ->addHandler(function (IMessengerContext $messenger, ChatRepository $chatRepo) {
                $chatRepo->update($messenger->get('chat'), [
                    'subscribed' => true,
                ]);

                return __('You subscribed to coronavirus pandemic updates');
            }, function (IMessengerContext $messenger) {
                return strpos($messenger->getMessage(), '/subscribe') === 0;
            })
            ->addHandler(function (IMessengerContext $messenger, ChatRepository $chatRepo) {
                $chatRepo->update($messenger->get('chat'), [
                    'subscribed' => false,
                ]);

                return __('You unsubscribed from coronavirus pandemic updates');
            }, function (IMessengerContext $messenger) {
                return strpos($messenger->getMessage(), '/unsubscribe') === 0;
            })
            ->addHandler(Chart::class, function (IMessengerContext $messenger) {
                return strpos($messenger->getMessage(), '/chart') === 0;
            })
            ->addHandler(NcovInfo::class, function (IMessengerContext $messenger) {
                return strpos($messenger->getMessage(), '/info') === 0;
            });
    }
}
