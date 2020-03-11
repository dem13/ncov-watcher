<?php

namespace App\BotKernel\User;

interface IBotUserManager
{
    public function setUser(IUser $user): IBotUserManager;

    public function setContext(?string $context): IBotUserManager;

    public function setPayload($payload): IBotUserManager;
}
