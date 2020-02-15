<?php

namespace App\Ncov\Crawler;

use GuzzleHttp\Client;

class WikipediaCrawler implements ICrawler
{
    private $client;

    private $uri = 'https://ru.wikipedia.org/wiki/%D0%92%D1%81%D0%BF%D1%8B%D1%88%D0%BA%D0%B0_%D0%BA%D0%BE%D1%80%D0%BE%D0%BD%D0%B0%D0%B2%D0%B8%D1%80%D1%83%D1%81%D0%B0_2019-nCoV';

    private $xpathQueries = [
        'deaths' => '//*[@id="mw-content-text"]/div/table[4]/tbody/tr[last()]/td[4]',
        'infected' => '//*[@id="mw-content-text"]/div/table[4]/tbody/tr[last()]/td[2]',
        'cured' => '//*[@id="mw-content-text"]/div/table[4]/tbody/tr[last()]/td[5]',
    ];

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Run crawler
     *
     * @return array
     */
    public function run(): array
    {
        $html = $this->getHtmlToCrawl();

        return $this->crawl($html);
    }

    /**
     * Get html to crawl
     *
     * @return string
     */
    private function getHtmlToCrawl(): string
    {
        $res = $this->client->request('GET', $this->uri);

        return $res->getBody()->getContents();
    }

    /**
     * Crawl html
     *
     * @param $html
     * @return array
     */
    private function crawl($html): array
    {
        $doc = new \DOMDocument();

        $doc->loadHTML($html);

        $xpath = new \DOMXPath($doc);

        $result = [];

        foreach ($this->xpathQueries as $key => $query) {
            $result[$key] = $xpath->query($query)[0]->textContent ?? null;
        }
        return $result;
    }
}
