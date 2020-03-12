<?php

namespace App\Telegram\Repositories;

use App\Telegram\Models\Chat;
use Illuminate\Support\Collection;

class ChatRepository
{
    /**
     * Add chat to database
     *
     * @param array $data
     * @return Chat
     */
    public function create(array $data): Chat
    {
        $chat = new Chat();

        $chat->id = $data['id'];

        $chat->fill($data);

        $chat->save();

        return $chat;
    }

    /**
     * Update chat
     *
     * @param Chat $chat
     * @param array $data
     * @return bool
     */
    public function update(Chat $chat, array $data): bool
    {
        $chat->fill($data);

        return $chat->save();
    }

    /**
     * Find chat by id
     *
     * @param $id
     * @return mixed
     */
    public function find($id): ?Chat
    {
        return Chat::find($id);
    }

    /**
     * Get subscribed chats
     *
     * @return Collection
     */
    public function getSubsbcribed(): Collection
    {
        return Chat::where('subscribed', true)->get();
    }
}
