<?php

namespace App\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_Filter('html_entity_decode', 'html_entity_decode')
        ];
    }
}
