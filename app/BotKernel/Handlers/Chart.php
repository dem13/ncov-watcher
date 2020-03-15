<?php

namespace App\BotKernel\Handlers;

use App\BotKernel\MessengerContexts\IMessengerContext;
use App\Ncov;
use App\Repositories\NcovRepository;
use App\Services\NcovService;

class Chart implements IMessageHandler
{

    /**
     * @var NcovService
     */
    private $ncovService;

    /**
     * @var NcovRepository
     */
    private $ncovRepo;

    /**
     * Chart constructor.
     *
     * @param NcovService $ncovService
     * @param NcovRepository $ncovRepo
     */
    public function __construct(NcovService $ncovService, NcovRepository $ncovRepo)
    {
        $this->ncovService = $ncovService;
        $this->ncovRepo = $ncovRepo;
    }

    public function handle(IMessengerContext $messenger)
    {
        $fields = ['infected', 'deaths', 'cured'];

        $message = explode('_', $messenger->getMessage());

        $ncov = $this->ncovRepo->getLast();

        if (count($message) !== 2) {
            $media = [];

            foreach ($fields as $field) {
                $media[] = [
                    'type' => 'photo',
                    'media' => url(\Storage::url($this->ncovService->getChartPath($field))),
                    'caption' => $this->chartCaption($field, $ncov)
                ];
            }

            $messenger->set('reply_media_group', $media);

            return __('COVID Charts');
        }

        $field = $message[1];

        if (!in_array($field, $fields, true)) {
            return __('Field not found');
        }


        $messenger->set('reply_photo', $this->ncovService->getChartPath($field));

        return $this->chartCaption($field, $ncov);
    }

    /**
     * Generate chart caption
     *
     * @param $field
     * @param Ncov $ncov
     * @return string
     */
    private function chartCaption($field, Ncov $ncov): string
    {
        return ucfirst($field) . ": {$ncov->{$field}}";
    }
}
