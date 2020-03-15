<?php

namespace App\Telegram;

use App\BotKernel\Bot as BotBase;
use App\BotKernel\MessengerContexts\TelegramMessengerContext;
use App\Telegram\Services\ChatService;
use App\Telegram\Services\UserService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class Bot
{
    /**
     * @var Api
     */
    private $telegram;

    /**
     * @var BotBase
     */
    private $bot;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ChatService
     */
    private $chatService;

    /**
     * @var Filesystem
     */
    private $storage;

    public function __construct(Api $telegram, BotBase $bot, UserService $userService, Filesystem $storage, ChatService $chatService)
    {
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->userService = $userService;
        $this->storage = $storage;
        $this->chatService = $chatService;
    }

    /**
     * @param Update $update
     * @throws \Exception
     */
    public function handleUpdate(Update $update)
    {
        if (!$chat = $update->getChat()) {
            return;
        }

        $localChat = $this->chatService->findOrCreate($chat);

        $messenger = new TelegramMessengerContext();

        $messenger->set('chat', $localChat);

        $from = null;

        if ($message = $update->getMessage()) {
            Log::info('message');
            $from = $message->getFrom();

            $messenger->setMessage($message->getText());

            if ($contact = $message->getContact()) {
                $messenger->set('contact', $contact);
            }

            if ($photo = $message->getPhoto()) {
                Log::info(print_r($photo, true));
                $messenger->set('photo', $photo);
            }
        }

        if ($callback = $update->getCallbackQuery()) {
            Log::info('callback');

            $from = $callback->getFrom();

            $messenger->set('callback', $callback);
        }

        if ($from === null) {
            return;
        }

        $user = $this->userService->findOrCreate($from);

        $messenger->setUser($user);

        $answer = $this->bot->handleMessage($messenger);

        Log::info($answer);

        if ($photo = $messenger->get('reply_photo')) {
            $this->telegram->sendPhoto([
                'chat_id' => $chat->getId(),
                'photo' => $this->storage->readStream($photo),
                'caption' => $answer
            ]);

            return;
        }

        if ($media = $messenger->get('reply_media_group')) {

            $this->telegram->sendMediaGroup([
                'chat_id' => $chat->getId(),
                'media' => json_encode($media),
            ]);

            return;
        }

        if (!$answer) {
            return;
        }

        $params = [
            'chat_id' => $chat->getId(),
            'text' => $answer,
        ];

        if ($keyboard = $messenger->get('keyboard')) {
            $params['reply_markup'] = $keyboard;
        }

        $this->telegram->sendMessage($params);
    }
}
