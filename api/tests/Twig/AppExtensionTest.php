<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtensionTest extends TestCase
{
    public function testHtmlEntityDecodeFilter(): void
    {
        $callable = $this->getFilterCallableFromExtension(
            new AppExtension($this->createMock(HtmlSanitizerInterface::class)),
            'html_entity_decode'
        );
        if (is_callable($callable)) {
            $result = $callable('&uuml;');
            $this->assertEquals('ü', $result);
        } else {
            $this->fail('Filter has no callable');
        }
    }

    public function testSanitizeFilter(): void
    {
        $purifier = $this->createMock(HtmlSanitizerInterface::class);
        $purifier
            ->expects($this->once())
            ->method('sanitize')
            ->with('foo')
            ->willReturn('bar');

        $callable = $this->getFilterCallableFromExtension(
            new AppExtension($purifier),
            'sanitize'
        );
        if (is_callable($callable)) {
            $result = $callable('foo');
            $this->assertEquals('bar', $result);
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
                $callable = $filter->getCallable();
                assert(is_callable($callable) || is_null($callable));
                return $callable;
            }
        }
        throw new \RuntimeException('Filter "' . $filterName . '" was not found.');
    }
}
