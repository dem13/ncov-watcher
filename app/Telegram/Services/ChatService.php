<?php


namespace App\Telegram\Services;


use App\Telegram\Models\Chat;
use App\Telegram\Repositories\ChatRepository;
use Telegram\Bot\Objects\Chat as TelegramChat;

class ChatService
{
    /**
     * @var ChatRepository
     */
    private $chatRepo;

    /**
     * ChatService constructor.
     *
     * @param ChatRepository $chatRepo
     */
    public function __construct(ChatRepository $chatRepo)
    {
        $this->chatRepo = $chatRepo;
    }

    /**
     * Find or create chat
     *
     * @param TelegramChat $telegramChat
     * @return Chat
     */
    public function findOrCreate(TelegramChat $telegramChat): Chat
    {
        if ($chat = $this->chatRepo->find($telegramChat->getId())) {
            return $chat;
        }

        return $this->chatRepo->create($telegramChat->toArray());
    }
}
