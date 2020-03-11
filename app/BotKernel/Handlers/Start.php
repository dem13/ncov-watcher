<?php

namespace App\BotKernel\Handlers;

use App\BotKernel\MessengerContexts\IMessengerContext;

class Start implements IMessageHandler
{
    public function handle(IMessengerContext $messenger)
    {
        return "Привет, я бот у которого ты можешь узнать о последствиях короновируса \n{$this->getCommandsDescription()}";
    }

    private function getCommandsDescription(): string
    {
        return "\n/subscribe - Подписаться на обновления \n/unsubscribe Отписаться от обновлений";
    }
}
