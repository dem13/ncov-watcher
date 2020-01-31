<?php

namespace App\Http\Controllers;

use App\Ncov\Crawler\ICrawler;
use App\Ncov\Exceptions\NcovDataIsEmptyException;
use App\Repositories\NcovRepository;
use App\Services\NcovService;
use Illuminate\Http\Response;

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
     * @return Response
     * @throws NcovDataIsEmptyException
     */
    public function crawl(ICrawler $crawler): Response
    {
        $ncovData = $crawler->run();

        $ncovData = $this->ncovService->validateNcovData($ncovData);

        $lastNcov = $this->ncovRepo->getLast();

        if ($this->ncovService->compare($lastNcov, $ncovData)) {
            return new Response("Data is same");
        }

        $this->ncovRepo->create($ncovData);

        //TODO: Notify users about change

        return new Response("Data changed");
    }
}
