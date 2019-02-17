<?php

namespace App\Tests\Service;

use App\Entity\Author;
use App\Entity\Feed;
use App\Entity\Item;
use App\Service\ItemExporter;
use Exercise\HTMLPurifierBundle\HTMLPurifiersRegistryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ItemExporterTest extends TestCase
{
    public function testExport()
    {
        /** @var UrlGeneratorInterface|MockObject $urlGenerator */
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator
            ->expects($this->atLeastOnce())
            ->method('generate')
            ->willReturn('https://www.archlinux.de/');
        /** @var \HTMLPurifier|MockObject $purifier */
        $purifier = $this->createMock(\HTMLPurifier::class);
        $purifier
            ->expects($this->atLeastOnce())
            ->method('purify')
            ->willReturn('Item Description');
        /** @var HTMLPurifiersRegistryInterface|MockObject $purifiersRegistry */
        $purifiersRegistry = $this->createMock(HTMLPurifiersRegistryInterface::class);
        $purifiersRegistry
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('planet')
            ->willReturn($purifier);

        $feed = (new Feed('https://www.archlinux.de/news/feed'))
            ->setTitle('Feed Title')
            ->setLink('https://www.archlinux.de/news')
            ->setLastModified(new \DateTime());
        $item = (new Item())
            ->setLink('https://www.archlinux.de/news/item')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime())
            ->setAuthor((new Author())->setName('Author Name')->setUri('Author URI'));
        $anotherItem = (new Item())
            ->setLink('https://www.archlinux.de/news/another-item')
            ->setTitle('Another Item Title')
            ->setDescription('Another Item Description')
            ->setLastModified(new \DateTime())
            ->setAuthor(new Author());
        $feed->addItem($item);
        $feed->addItem($anotherItem);

        $itemExporter = new ItemExporter($urlGenerator, $purifiersRegistry);

        $atomFeed = $itemExporter->export([$item, $anotherItem], 'atom');
        $this->assertStringContainsString('Item Title', $atomFeed);
        $this->assertStringContainsString('Another Item Title', $atomFeed);
    }
}
