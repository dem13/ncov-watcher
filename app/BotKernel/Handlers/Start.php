<?php

namespace App\BotKernel\Handlers;

use App\BotKernel\MessengerContexts\IMessengerContext;

class Start implements IMessageHandler
{
    public function handle(IMessengerContext $messenger)
    {
        //TODO: Translate

        return "Привет, я бот у которого ты можешь узнать о распространении коронавируса \n\n{$this->getCommandsDescription()}";
    }

    private function getCommandsDescription(): string
    {
        $commands = [
            'help' => 'Узнать доступные команды❔ ',
            'info' => 'Получить количество зараженных, умерших и вылеченных😈 ',
            'subscribe' => 'Подписаться на обновления➕ ',
            'unsubscribe' => 'Отписаться от обновлений➖ ',
            'chart' => 'Получить график по зараженным, умершим и вылеченным📊 ',
            'chart_infected' => 'Получить график по зараженным📈 ',
            'chart_deaths' => 'Получить график по умершим📉 ',
            'chart_cured' => 'Получить график по вылеченным🚑 '
        ];

        $answer = "\n";

        foreach ($commands as $command => $desc) {
            $answer .= "/{$command} - {$desc}\n";
        }

        return $answer;
    }
}
