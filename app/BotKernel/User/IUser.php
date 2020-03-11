<?php

namespace App\BotKernel\User;

interface IUser
{
    public function getId();

    public function getContext();

    public function getName();

    public function getPayload();
}
