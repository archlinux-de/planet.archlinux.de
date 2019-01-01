<?php

namespace App\Twig;

use App\Entity\Item;
use Exercise\HTMLPurifierBundle\HTMLPurifiersRegistryInterface;

class AppExtension extends \Twig_Extension
{
    /** @var HTMLPurifiersRegistryInterface */
    private $purifiersRegistry;

    /**
     * @param HTMLPurifiersRegistryInterface $purifiersRegistry
     */
    public function __construct(HTMLPurifiersRegistryInterface $purifiersRegistry)
    {
        $this->purifiersRegistry = $purifiersRegistry;
    }

    public function getFilters()
    {
        return [
            new \Twig_Filter('item_title', [$this, 'itemTitle']),
            new \Twig_Filter('item_description', [$this, 'itemDescription'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param Item $item
     *
     * @return string
     */
    public function itemTitle(Item $item): string
    {
        return html_entity_decode($item->getTitle());
    }

    /**
     * @param Item $item
     *
     * @return string
     */
    public function itemDescription(Item $item): string
    {
        $newConfig = \HTMLPurifier_Config::inherit($this->purifiersRegistry->get('planet')->config);
        $newConfig->set('URI.Base', $item->getLink());

        return (new \HTMLPurifier($newConfig))->purify($item->getDescription());
    }
}
