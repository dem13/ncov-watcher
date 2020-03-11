<?php

namespace App\Telegram\Services;

use App\Telegram\Models\User;
use App\Telegram\Repositories\UserRepository;
use Telegram\Bot\Objects\User as TelegramUser;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Find user by user id taken which is taken from
     * telegram update or create user if user doesn't exists
     *
     * @param TelegramUser $telegramUser
     * @return User
     */
    public function findOrCreate(TelegramUser $telegramUser): User
    {
        if ($user = $this->userRepo->find($telegramUser->getId())) {
            return $user;
        }

        return $this->userRepo->create([
            'id' => $telegramUser->getId(),
            'username' => $telegramUser->getUsername(),
            'first_name' => $telegramUser->getFirstName(),
            'last_name' => $telegramUser->getLastName(),
            'language_code' => $telegramUser->get('language_code'),
            'is_bot' => $telegramUser->getIsBot(),
        ]);
    }
}
