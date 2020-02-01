<?php

namespace App\Http\Controllers;

use App\Ncov\Chart\Chart;
use App\Ncov\Chart\ChartRecord;
use App\Ncov\Crawler\ICrawler;
use App\Ncov\Exceptions\NcovDataIsEmptyException;
use App\Repositories\NcovRepository;
use App\Services\NcovService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Telegram\Bot\Api;

class NcovController extends Controller
{
    /**
     * @var NcovRepository
     */
    private $ncovRepo;

    /**
     * @var NcovService
     */
    private $ncovService;

    /**
     * NcovController constructor.
     *
     * @param NcovRepository $ncovRepo
     * @param NcovService $ncovService
     */
    public function __construct(NcovRepository $ncovRepo, NcovService $ncovService)
    {
        $this->ncovRepo = $ncovRepo;
        $this->ncovService = $ncovService;
    }

    /**
     * Crawl ncov data and store it in database
     *
     * TODO: REFACTOR
     * TODO: Get users to notify from database
     *
     * @param ICrawler $crawler
     * @param Api $teleram
     * @param Filesystem $storage
     * @return Response
     * @throws NcovDataIsEmptyException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function crawl(ICrawler $crawler, Api $teleram, Filesystem $storage): Response
    {
        $ncovData = $crawler->run();

        $ncovData = $this->ncovService->validateNcovData($ncovData);

        $lastNcov = $this->ncovRepo->getLast();

        if ($this->ncovService->compare($lastNcov, $ncovData)) {
            return new Response('Data is same');
        }

        $ncov = $this->ncovRepo->create($ncovData);

        $chart = new Chart();

        foreach (['infected', 'deaths', 'cured'] as $field) {


            $records = [];


            foreach ($this->ncovRepo->get() as $item) {
                $records[] = new ChartRecord($item->{$field}, $item->created_at);
            }

            $chart->setRecords($records);

            $image = $chart->render();

            $imagePath = "chart/ncov/{$ncov->id}_{$field}.png";

            $storage->put($imagePath, $image);

            $teleram->sendPhoto([
                'chat_id' => config('ncov.report.telegram'),
                'photo' => $storage->readStream($imagePath),
                'caption' => "{$field}: {$ncov->{$field}}",
            ]);
        }

        return new Response('Data changed');
    }

    public function chart(string $field, Filesystem $storage): Response
    {
        if (!in_array($field, ['deaths', 'infected', 'cured'])) {
            throw new NotFoundHttpException();
        }

        $ncov = $this->ncovRepo->getLast();

        $chartImage = "chart/ncov/{$ncov->id}_{$field}.png";

        if ($storage->exists($chartImage)) {
            return new Response($storage->get($chartImage), 200, [
                'Content-type' => 'image/png'
            ]);
        }

        $chart = new Chart();

        $records = [];

        foreach ($this->ncovRepo->get() as $ncov) {
            $records[] = new ChartRecord($ncov->{$field}, $ncov->created_at);
        }

        $chart->setRecords($records);

        $image = $chart->render();

        $storage->put($chartImage, $image);

        return new Response($image, 200, [
            'Content-Type' => 'image/png',
        ]);
    }
}
