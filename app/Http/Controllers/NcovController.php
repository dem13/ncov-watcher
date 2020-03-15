<?php

namespace App\Http\Controllers;

use App\Ncov\Crawler\ICrawler;
use App\Ncov\Exceptions\NcovDataIsEmptyException;
use App\Repositories\NcovRepository;
use App\Services\NcovService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
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
     * @param ICrawler $crawler
     * @param Api $telegram
     * @param Filesystem $storage
     * @return Response|string
     * @throws NcovDataIsEmptyException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function crawl(ICrawler $crawler, Api $telegram, Filesystem $storage)
    {
        if (!$ncov = $this->ncovService->checkForUpdates($crawler, $telegram, $storage)) {
            return new Response('Data is same');
        }

        return new Response($ncov->toJson(), 200, [
            'Content-type' => 'application/json'
        ]);
    }

    public function chart(string $field, Request $request, Filesystem $storage): Response
    {
        if (!in_array($field, ['deaths', 'infected', 'cured'])) {
            throw new NotFoundHttpException();
        }

        $image = $this->ncovService->getChart($field);

        return new Response($image, 200, [
            'Content-Type' => 'image/png',
        ]);
    }
}
