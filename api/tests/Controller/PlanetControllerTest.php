<?php

namespace App\Tests\Controller;

use App\Controller\PlanetController;
use App\Entity\Feed;
use App\Entity\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;
use SymfonyDatabaseTest\DatabaseTestCase;

#[CoversClass(PlanetController::class)]
class PlanetControllerTest extends DatabaseTestCase
{
    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
        $feed = new Feed('https://www.archlinux.de/')
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');
        $oldItem = new Item('https://www.archlinux.de/news/1')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('- 2 day'))
            ->setFeed($feed);
        $newItem = new Item('https://www.archlinux.de/news/2')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('now'))
            ->setFeed($feed);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($oldItem);
        $entityManager->persist($newItem);
        $entityManager->flush();
        $entityManager->clear();
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
