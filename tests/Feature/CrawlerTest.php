<?php

namespace Tests\Feature;

use App\Ncov\Crawler\ICrawler;
use Tests\TestCase;


class CrawlerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCralwerReturnsAssocArrayOfNumbers()
    {
        $crawler = resolve(ICrawler::class);

        $ncov = $crawler->run();

        $this->assertIsNumeric($ncov['deaths']);
        $this->assertIsNumeric($ncov['infected']);
        $this->assertIsNumeric($ncov['cured']);

    }
}
