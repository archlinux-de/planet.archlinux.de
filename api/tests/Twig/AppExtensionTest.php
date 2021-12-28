<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use HTMLPurifier;
use PHPUnit\Framework\TestCase;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtensionTest extends TestCase
{
    public function testHtmlEntityDecodeFilter(): void
    {
        $callable = $this->getFilterCallableFromExtension(
            new AppExtension($this->createMock(HTMLPurifier::class)),
            'html_entity_decode'
        );
        if (is_callable($callable)) {
            $result = $callable('&uuml;');
            $this->assertEquals('ü', $result);
        } else {
            $this->fail('Filter has no callable');
        }
    }

    public function testPurifyFilter(): void
    {
        $purifier = $this->createMock(HTMLPurifier::class);
        $purifier
            ->expects($this->once())
            ->method('purify')
            ->with('foo')
            ->willReturn('bar');

        $callable = $this->getFilterCallableFromExtension(
            new AppExtension($purifier),
            'purify'
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
                return $filter->getCallable();
            }
        }
        throw new \RuntimeException('Filter "' . $filterName . '" was not found.');
    }
}
