<?php

namespace App\BotKernel\Handlers;

use App\BotKernel\MessengerContexts\IMessengerContext;

class HandlerDecorator implements IMessageHandler
{
    /**
     * @var string
     */
    private $handler;

    /**
     * @var
     */
    private $context;

    /**
     * @var mixed
     */
    private $pattern;

    /**
     * HandlerDecorator constructor.
     *
     * @param string $handler
     * @param $context
     * @param $filter
     * @param $context1
     */
    public function __construct($handler, $pattern, $context)
    {
        $this->handler = $handler;
        $this->context = $context;
        $this->pattern = $pattern;
    }

    /**
     * Handle message to bot
     *
     * @param IMessengerContext $messenger
     * @return mixed
     * @throws \Exception
     */
    public function handle(IMessengerContext $messenger)
    {
        if ($this->handler instanceof \Closure) {
            return app()->call($this->handler, ['messenger' => $messenger]);
        }

        $handler = resolve($this->handler);

        if ($handler instanceof IMessageHandler) {
            return $handler->handle($messenger);
        }

        throw new \Exception('Invalid message handler type');
    }

    /**
     * Check if the handler should process this message
     *
     * @param IMessengerContext $context
     * @return bool
     */
    public function match(IMessengerContext $context): bool
    {
        $message = $context->getMessage();

        $pattern = $this->pattern;

        if ($pattern instanceof \Closure) {

            return $pattern($context);
        }

        if (is_bool($pattern)) {

            return $pattern;
        }

        if (is_array($pattern)) {

            return in_array($message, $pattern);
        }

        return $context->getMessage() === $this->pattern;
    }

    /**
     * Get handler context
     *
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }
}
