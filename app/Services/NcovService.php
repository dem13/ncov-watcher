<?php

namespace App\Services;

use App\Ncov;
use App\Ncov\Chart\Chart;
use App\Ncov\Chart\ChartRecord;
use App\Ncov\Crawler\ICrawler;
use App\Ncov\Exceptions\NcovDataIsEmptyException;
use App\Repositories\NcovRepository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Telegram\Bot\Api;

class NcovService
{
    private $keys = ['deaths', 'infected', 'cured'];

    /**
     * @var NcovRepository
     */
    private $ncovRepo;

    /**
     * @var TelegramService
     */
    private $telegramService;

    /**
     * NcovService constructor.
     * @param NcovRepository $ncovRepo
     * @param TelegramService $telegramService
     */
    public function __construct(NcovRepository $ncovRepo, TelegramService $telegramService)
    {
        $this->ncovRepo = $ncovRepo;
        $this->telegramService = $telegramService;
    }

    /**
     * Check if ncov model is equal to ncov data
     *
     * @param Ncov $ncov
     * @param array $ncovData
     * @return bool
     */
    public function compare(Ncov $ncov, array $ncovData): bool
    {
        foreach ($this->keys as $key) {
            if ((int)$ncovData[$key] !== $ncov->{$key}) {
                return false;
            }
        }

        return true;
    }


    /**
     * Check if ncov data is not empty
     *
     * @param array $ncov
     * @return array
     * @throws NcovDataIsEmptyException
     */
    public function validateNcovData(array $ncov): array
    {
        $result = [];

        foreach ($this->keys as $key) {
            if (empty($ncov[$key])) {
                throw new NcovDataIsEmptyException("Ncov data is empty");
            }

            $result[$key] = $ncov[$key];
        }

        return $result;
    }

    /**
     * @param ICrawler $crawler
     * @param Api $telegram
     * @param Filesystem $storage
     * @return Ncov|null
     * @throws NcovDataIsEmptyException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function checkForUpdates(ICrawler $crawler, Api $telegram, Filesystem $storage): ?Ncov
    {
        $ncovData = $crawler->run();

        $ncovData = $this->validateNcovData($ncovData);

        $lastNcov = $this->ncovRepo->getLast();

        if ($this->compare($lastNcov, $ncovData)) {
            return null;
        }

        $ncov = $this->ncovRepo->create($ncovData);

        foreach (['infected', 'deaths', 'cured'] as $field) {
            ;

            $imagePath = "chart/ncov/{$ncov->id}_{$field}.png";

            $storage->put($imagePath, $this->createChart($field));

            $difference = $ncov->{$field} - $lastNcov->{$field};

            $this->telegramService->sendPhotoToSubscribers($imagePath, [
                'caption' => "{$field}: {$ncov->{$field}} (" . ($difference >= 0 ? '+' : '') . $difference . ')',
                'disable_notification' => true
            ]);
        }

        return $ncov;
    }

    public function createChart($field)
    {
        $chart = new Chart();

        $records = [];

        foreach ($this->ncovRepo->getLatestForEachDay() as $item) {
            $records[] = new ChartRecord($item->{$field}, $item->created_at);
        }

        $chart->setRecords($records);

        return $chart->render();
    }
}
