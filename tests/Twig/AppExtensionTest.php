<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtensionTest extends TestCase
{
    public function testHtmlEntityDecodeFilter()
    {
        $callable = $this->getFilterCallableFromExtension(new AppExtension(), 'html_entity_decode');
        if (is_callable($callable)) {
            $result = call_user_func(
                $callable,
                '&uuml;'
            );
            $this->assertEquals('ü', $result);
        } else {
            $this->fail('Filter has no callable');
        }
    }

    public function testImageLoadingFilter()
    {
        $callable = $this->getFilterCallableFromExtension(new AppExtension(), 'img_loading');
        if (is_callable($callable)) {
            $result = call_user_func(
                $callable,
                '<img src="foo.png"/>',
                'lazy'
            );
            $this->assertEquals('<img loading="lazy" src="foo.png"/>', $result);
        } else {
            $this->fail('Filter has no callable');
        }
    }

    /**
     * @param AbstractExtension $extension
     * @param string $filterName
     * @return callable|null
     */
    private function getFilterCallableFromExtension(AbstractExtension $extension, string $filterName): ?callable
    {
        /** @var TwigFilter $filter */
        foreach ($extension->getFilters() as $filter) {
            if ($filter->getName() == $filterName) {
                return $filter->getCallable();
            }
        }
        throw new \RuntimeException('Filter "' . $filterName . '" was not found.');
    }
}
