<?php

namespace App\Services;

use App\Telegram\Repositories\ChatRepository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Telegram\Bot\Api;

class TelegramService
{
    /**
     * @var Api
     */
    private $telegram;

    /**
     * @var ChatRepository
     */
    private $chatRepo;

    /**
     * @var Filesystem
     */
    private $storage;

    public function __construct(Api $telegram, ChatRepository $chatRepo, Filesystem $storage)
    {
        $this->telegram = $telegram;
        $this->chatRepo = $chatRepo;
        $this->storage = $storage;
    }

    /**
     * Send message to all subscribed chats
     *
     * @param $message
     * @param array $params
     * @return array
     */
    public function sendMessageToSubscribers($message, $params = []): array
    {
        if (empty($message)) {
            return [];
        }

        $params['text'] = $message;

        return $this->sendForEachSubscriber(fn ($params) => $this->telegram->sendMessage($params), $params);
    }

    /**
     * Send telegram message to all subscribers
     *
     * @param \Closure $callback
     * @param array $params
     * @return array
     */
    private function sendForEachSubscriber(\Closure $callback, $params = []): array
    {
        $chats = $this->chatRepo->getSubsbcribed();

        $sent = 0;
        $failed = 0;


        foreach ($chats as $chat) {
            try {
                $params['chat_id'] = $chat->id;

                $callback($params);

                $sent++;
            } catch (\Exception $e) {
                $failed++;

                \Log::error($e->getMessage());
            }
        }

        return [
            'total' => $chats->count(),
            'sent' => $sent,
            'failed' => $failed,
        ];
    }

    /**
     * Send photo to all subscribers
     *
     * @param $path
     * @param array $params
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function sendPhotoToSubscribers($path, $params = []): array
    {
        if (!$this->storage->exists($path)) {
            return [];
        }

        return $this->sendForEachSubscriber(function ($params) use ($path) {
            $params['photo'] = $this->storage->readStream($path);
            $this->telegram->sendPhoto($params);
        }, $params);
    }
}
