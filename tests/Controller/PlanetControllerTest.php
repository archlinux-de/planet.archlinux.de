<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use SymfonyDatabaseTest\DatabaseTestCase;

/**
 * @covers \App\Controller\PlanetController
 */
class PlanetControllerTest extends DatabaseTestCase
{
    public function testIndexAction(): void
    {
        $client = $this->getClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('Arch Linux Planet', $crawler->filter('h1')->text());
    }

    public function testAtomAction(): void
    {
        $client = $this->getClient();

        $client->request('GET', '/atom.xml');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringStartsWith(
            'application/atom+xml; charset=UTF-8',
            (string)$client->getResponse()->headers->get('Content-Type')
        );
        $this->assertEquals('UTF-8', $client->getResponse()->getCharset());
        $response = $client->getResponse()->getContent();
        $this->assertIsString($response);
        $xml = \simplexml_load_string($response);
        $this->assertNotFalse($xml);
        $this->assertEmpty(\libxml_get_errors());
        $this->assertEquals('Arch Linux Planet', (string)$xml->title);
    }

    public function testRssAction(): void
    {
        $client = $this->getClient();

        $client->request('GET', '/rss.xml');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringStartsWith(
            'application/rss+xml; charset=UTF-8',
            (string)$client->getResponse()->headers->get('Content-Type')
        );
        $this->assertEquals('UTF-8', $client->getResponse()->getCharset());
        $response = $client->getResponse()->getContent();
        $this->assertIsString($response);
        $xml = \simplexml_load_string($response);
        $this->assertNotFalse($xml);
        $this->assertEmpty(\libxml_get_errors());
        $this->assertEquals('Arch Linux Planet', (string)$xml->channel->title);
    }

    public function testLegacyRssUrlGetsRedirected(): void
    {
        $client = $this->getClient();

        $client->request('GET', '/rss20.xml');

        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/rss.xml'));
    }
}
