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
     * @var Filesystem
     */
    private $storage;

    /**
     * NcovService constructor.
     * @param NcovRepository $ncovRepo
     * @param TelegramService $telegramService
     * @param Filesystem $storage
     */
    public function __construct(NcovRepository $ncovRepo, TelegramService $telegramService, Filesystem $storage)
    {
        $this->ncovRepo = $ncovRepo;
        $this->telegramService = $telegramService;
        $this->storage = $storage;
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

    /**
     * Create ncov chart for one of the field
     *
     * @param $field
     * @return string
     */
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

    /**
     * Get chart existed chart or create one if doesn't exits
     *
     * @param $field
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getChart($field)
    {
        $ncov = $this->ncovRepo->getLast();

        $path = $this->chartPath($ncov->id, $field);

        if ($this->storage->exists($path)) {
            return $this->storage->get($path);
        }

        $chart = $this->createChart($field);

        $this->storage->put($path, $chart);

        return $chart;
    }

    /**
     * Get path to existed chart or to created one
     *
     * @param $field
     * @return string
     */
    public function getChartPath($field)
    {
        $ncov = $this->ncovRepo->getLast();

        $path = $this->chartPath($ncov->id, $field);

        if (!$this->storage->exists($path)) {
            $chart = $this->createChart($field);

            $this->storage->put($path, $chart);
        }

        return $path;
    }

    /**
     * Generate chart path
     *
     * @param $id
     * @param $postfix
     * @return string
     */
    private function chartPath($id, $postfix)
    {
        return "chart/ncov/chart_{$id}_{$postfix}.png";
    }
}
