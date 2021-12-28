<?php

namespace App\Twig;

use HTMLPurifier;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(private HTMLPurifier $purifier)
    {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('html_entity_decode', 'html_entity_decode'),
            new TwigFilter('purify', [$this->purifier, 'purify'], ['is_safe' => ['html']])
        ];
    }
}
