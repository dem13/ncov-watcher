<?php

namespace App\Telegram\Repositories;

use App\Telegram\Models\User;

class UserRepository
{
    /**
     * Add user to database
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        $user = new User();

        $user->id = $data['id'];
        $user->username = $data['username'];

        $user->fill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'language_code' => $data['language_code'],
            'is_bot' => $data['is_bot']
        ]);

        $user->save();

        return $user;
    }

    /**
     * Update user data
     *
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function update(User $user, array $data)
    {
        $user->fill($data);

        return $user->save();
    }

    /**
     * Find user by id
     *
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }
}
