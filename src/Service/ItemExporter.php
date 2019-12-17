<?php

namespace App\Service;

use App\Entity\Item;
use Exercise\HTMLPurifierBundle\HTMLPurifiersRegistryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zend\Feed\Writer\Feed;

class ItemExporter
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var HTMLPurifiersRegistryInterface */
    private $purifiersRegistry;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param HTMLPurifiersRegistryInterface $purifiersRegistry
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        HTMLPurifiersRegistryInterface $purifiersRegistry
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->purifiersRegistry = $purifiersRegistry;
    }

    /**
     * @param Item[] $items
     * @param string $format
     * @return string
     */
    public function export(array $items, string $format): string
    {
        $feed = new Feed();
        $feed->setTitle('Arch Linux Planet')
            ->setDateModified()
            ->setId(
                $this->urlGenerator->generate(
                    'app_planet_feed',
                    ['_format' => $format],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            )
            ->setLink(
                $this->urlGenerator->generate(
                    'app_planet_index',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            )
            ->setFeedLink(
                $this->urlGenerator->generate(
                    'app_planet_feed',
                    ['_format' => $format],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $format
            )
            ->setDescription('planet.archlinux.de');

        foreach ($items as $item) {
            $entry = $feed->createEntry();

            $source = $entry->createSource();
            $source
                ->setTitle($item->getFeed()->getTitle())
                ->setLink($item->getFeed()->getLink())
                ->setDateModified($item->getFeed()->getLastModified())
                ->setFeedLink($item->getFeed()->getUrl(), 'atom');

            $entry
                ->setLink($item->getLink())
                ->setTitle($item->getTitle())
                ->setDescription($this->purifiersRegistry->get('planet')->purify($item->getDescription()))
                ->setDateModified($item->getLastModified())
                ->setSource($source);

            if ($format == 'atom') {
                if ($item->getAuthor()->getName()) {
                    $entry->
                    addAuthor([
                        'name' => $item->getAuthor()->getName(),
                        'uri' => $item->getAuthor()->getUri() ?? $item->getFeed()->getLink()
                    ]);
                } else {
                    $entry->addAuthor(['name' => $item->getFeed()->getTitle()]);
                };
            }

            $feed->addEntry($entry);
        }

        return $feed->export($format);
    }
}
