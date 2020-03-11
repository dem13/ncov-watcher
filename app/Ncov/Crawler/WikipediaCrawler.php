<?php

namespace App\Ncov\Crawler;

use GuzzleHttp\Client;

class WikipediaCrawler implements ICrawler
{
    private $client;

    private $uri = 'https://ru.wikipedia.org/wiki/%D0%92%D1%81%D0%BF%D1%8B%D1%88%D0%BA%D0%B0_COVID-19';

    private $xpathQueries = [
        //*[@id="mw-content-text"]/div/table[2]/tbody/tr[125]/td[2]/b
        'deaths' => '//*[@id="mw-content-text"]/div/table[2]/tbody/tr[last()]/td[4]',
        'infected' => '//*[@id="mw-content-text"]/div/table[2]/tbody/tr[last()]/td[2]',
        'cured' => '//*[@id="mw-content-text"]/div/table[2]/tbody/tr[last()]/td[5]',
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
            $result[$key] = str_replace(' ', '', $xpath->query($query)[0]->textContent);
        }
        return $result;
    }
}
