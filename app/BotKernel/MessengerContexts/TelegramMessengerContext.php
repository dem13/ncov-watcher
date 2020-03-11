<?php

namespace App\BotKernel\MessengerContexts;

use App\BotKernel\User\IBotUserManager;
use App\BotKernel\User\IUser;

class TelegramMessengerContext implements IMessengerContext
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @var mixed
     */
    private $message;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * @var IUser
     */
    private $user;


    public function getMessenger()
    {
        return 'telegram';
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get attribute
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Set attribute
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get user manager
     *
     * @return IBotUserManager
     */
    public function getUserManager(): IBotUserManager
    {
        return resolve(IBotUserManager::class)->setUser($this->getUser());
    }

    public function getUser(): ?IUser
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
