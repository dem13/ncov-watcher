<?php

namespace App\BotKernel;

use App\BotKernel\Handlers\HandlerDecorator;
use App\BotKernel\MessengerContexts\IMessengerContext;

class Bot
{
    /**
     * @var HandlerDecorator[]
     */
    private $handlers = [];

    /**
     * Add message handler
     *
     * @param mixed $handler
     * @param $pattern
     * @param null $context
     * @return $this
     */
    public function addHandler($handler, $pattern, $context = null)
    {
        $this->handlers[] = new HandlerDecorator($handler, $pattern, $context);

        return $this;
    }

    /**
     * Handle message for bot
     *
     * @param IMessengerContext $messenger
     * @return string
     * @throws \Exception
     */
    public function handleMessage(IMessengerContext $messenger)
    {
        $userContext = $messenger->getUser() ?
            $messenger->getUser()->getContext() :
            null;

        $messageBack = null;


        \Log::debug(print_r($this->handlers, true));

        foreach ($this->handlers as $handler) {
            $context = $handler->getContext();
            if (($userContext === $context || $context === true) && $handler->match($messenger)) {
                return $handler->handle($messenger);
            }
        }

        return $messageBack;
    }
}
