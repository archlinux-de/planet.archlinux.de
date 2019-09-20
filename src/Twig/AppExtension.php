<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('html_entity_decode', 'html_entity_decode'),
            new TwigFilter('img_loading', [$this, 'addImageLoading'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param string $html
     * @param string $loading
     * @return string
     */
    public function addImageLoading(string $html, string $loading): string
    {
        return (string)preg_replace(
            '/(<img)/i',
            sprintf('\1 loading="%s"', preg_quote($loading, '/')),
            $html
        );
    }
}
