<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;

class AppExtensionTest extends TestCase
{
    public function testGetFilters()
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

    /**
     * @param \Twig_Extension $extension
     * @param string $filterName
     * @return callable|null
     */
    private function getFilterCallableFromExtension(\Twig_Extension $extension, string $filterName): ?callable
    {
        /** @var \Twig_Filter $filter */
        foreach ($extension->getFilters() as $filter) {
            if ($filter->getName() == $filterName) {
                return $filter->getCallable();
            }
        }
        throw new \RuntimeException('Filter "' . $filterName . '" was not found.');
    }
}
