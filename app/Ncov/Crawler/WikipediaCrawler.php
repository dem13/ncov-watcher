<?php

namespace App\Ncov\Crawler;

use GuzzleHttp\Client;

class WikipediaCrawler implements ICrawler
{
    private $client;

    private $uri = 'https://en.wikipedia.org/wiki/2019%E2%80%9320_coronavirus_pandemic';

    private $xpathQueries = [
        'deaths' => '//*[@id="mw-content-text"]/div/table[3]/tbody/tr[3]/th[3]',
        'infected' => '//*[@id="mw-content-text"]/div/table[3]/tbody/tr[3]/th[2]',
        'cured' => '//*[@id="mw-content-text"]/div/table[3]/tbody/tr[3]/th[4]',
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

        $doc->loadHTML($html, LIBXML_NOERROR);

        $xpath = new \DOMXPath($doc);

        $result = [];

        foreach ($this->xpathQueries as $key => $query) {
            $result[$key] = str_replace(',', '', trim($xpath->query($query)[0]->textContent));
        }
        return $result;
    }
}
