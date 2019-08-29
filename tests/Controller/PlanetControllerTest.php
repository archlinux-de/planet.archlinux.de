<?php

namespace App\Tests\Controller;

use SymfonyDatabaseTest\DatabaseTestCase;

/**
 * @covers \App\Controller\PlanetController
 */
class PlanetControllerTest extends DatabaseTestCase
{
    public function testIndexAction()
    {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('Arch Linux Planet', $crawler->filter('h1')->text());
    }

    public function testAtomAction()
    {
        $client = $this->getClient();

        $client->request('GET', '/atom.xml');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $response = $client->getResponse()->getContent();
        $this->assertIsString($response);
        $xml = \simplexml_load_string($response);
        $this->assertNotFalse($xml);
        $this->assertEmpty(\libxml_get_errors());
        $this->assertEquals('Arch Linux Planet', $xml->title->__toString());
    }

    /**
     * @param string $rssURl
     * @dataProvider provideRssUrls
     */
    public function testRssAction(string $rssURl)
    {
        $client = $this->getClient();

        $client->request('GET', $rssURl);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $response = $client->getResponse()->getContent();
        $this->assertIsString($response);
        $xml = \simplexml_load_string($response);
        $this->assertNotFalse($xml);
        $this->assertEmpty(\libxml_get_errors());
        $this->assertEquals('Arch Linux Planet', $xml->channel->title->__toString());
    }

    /**
     * @return array
     */
    public function provideRssUrls(): array
    {
        return [
            ['/rss.xml'],
            ['/rss20.xml']
        ];
    }
}
