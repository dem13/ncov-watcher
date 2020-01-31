<?php

namespace App\Ncov\Crawler;

interface ICrawler
{
    /**
     * Run crawler
     *
     * @return array
     */
    public function run(): array;
}
