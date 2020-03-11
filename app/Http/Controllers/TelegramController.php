<?php

namespace App\Http\Controllers;

use App\Telegram\Bot;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    /**
     * @var Api
     */
    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Set up webhook
     *
     * @return Response
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function setupWebhook()
    {
        $response = $this->telegram->setWebhook([
            'url' => route('telegram.update')
        ]);

        return new Response($response->getBody());
    }

    /**
     * Incoming telegram update
     * @throws \Exception
     */
    public function update(Bot $bot)
    {
        $update = $this->telegram->getWebhookUpdate();

        Log::debug(print_r($update, true));

        $bot->handleUpdate($update);
    }
}
