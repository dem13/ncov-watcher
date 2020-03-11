<?php

namespace App\BotKernel\MessengerContexts;

use App\BotKernel\User\IBotUserManager;
use App\BotKernel\User\IUser;

interface IMessengerContext
{
    /**
     * Get messenger name
     *
     * @return mixed
     */
    public function getMessenger();

    /**
     * Get incoming message
     *
     * @return mixed
     */
    public function getMessage();

    /**
     * Get user
     *
     * @return IUser|null
     */
    public function getUser(): ?IUser;

    /**
     * Get user manager
     *
     * @return IBotUserManager
     */
    public function getUserManager(): IBotUserManager;

    /**
     * Get payload
     *
     * @return mixed
     */
    public function getPayload();

    /**
     * Get attribute
     *
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * Set attribute
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);
}
