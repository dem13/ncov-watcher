<?php

namespace App\BotKernel\Handlers;

use App\BotKernel\MessengerContexts\IMessengerContext;

interface IMessageHandler
{
    public function handle(IMessengerContext $messenger);
}
