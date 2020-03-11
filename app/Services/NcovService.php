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
     * NcovService constructor.
     * @param NcovRepository $ncovRepo
     */
    public function __construct(NcovRepository $ncovRepo)
    {
        $this->ncovRepo = $ncovRepo;
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

        $chart = new Chart();

        foreach (['infected', 'deaths', 'cured'] as $field) {

            $records = [];

            foreach ($this->ncovRepo->getLatestForEachDay() as $item) {
                $records[] = new ChartRecord($item->{$field}, $item->created_at);
            }

            $chart->setRecords($records);

            $image = $chart->render();

            $imagePath = "chart/ncov/{$ncov->id}_{$field}.png";

            $storage->put($imagePath, $image);

            //TODO: Get chat ids from db

            foreach ([config('ncov.report.telegram'), '-380424566', '-378556426'] as $chatId) {

                //TODO: save uploaded photo id

                $difference = $ncov->{$field} - $lastNcov->{$field};

                $telegram->sendPhoto([
                    'chat_id' => $chatId,
                    'photo' => $storage->readStream($imagePath),
                    'caption' => "{$field}: {$ncov->{$field}} (" . ($difference >= 0 ? '+' : '') . $difference . ')',
                ]);
            }
        }

        return $ncov;
    }
}
