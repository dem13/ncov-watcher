<?php

namespace App\BotKernel\Handlers;

use App\BotKernel\MessengerContexts\IMessengerContext;
use App\Repositories\NcovRepository;

class NcovInfo implements IMessageHandler
{
    /**
     * @var NcovRepository
     */
    private $ncovRepo;

    public function __construct(NcovRepository $ncovRepo)
    {
        $this->ncovRepo = $ncovRepo;
    }

    public function handle(IMessengerContext $messenger)
    {
        $answer = '';

        $ncov = $this->ncovRepo->getLast();

        foreach (['infected', 'deaths', 'cured'] as $field) {
            $answer .= ucfirst($field) . ': ' . $ncov->{$field} . "\n";
        }

        return $answer;
    }
}
