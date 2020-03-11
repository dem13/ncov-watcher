<?php

namespace App\BotKernel\User;

use App\User;
use Illuminate\Database\Eloquent\Model;

class EloquentUserManager implements IBotUserManager
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param IUser $user
     * @return IBotUserManager
     * @throws \Exception
     */
    public function setUser(IUser $user): IBotUserManager
    {
        if (!$user instanceof Model) {
            throw new \Exception('Eloquent user manager works only with eloquent models');
        }

        $this->user = $user;

        return $this;
    }


    /**
     * @param string $context
     * @return IBotUserManager
     */
    public function setContext(?string $context): IBotUserManager
    {
        $this->user->context = $context;

        $this->user->save();

        return $this;
    }

    /**
     * @param $payload
     * @return IBotUserManager
     */
    public function setPayload($payload): IBotUserManager
    {
        $this->user->payload = json_encode($payload);

        $this->user->save();

        return $this;
    }
}
