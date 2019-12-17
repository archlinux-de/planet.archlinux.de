<?php

namespace App\Tests\Entity;

use App\Entity\Author;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    public function testEntity(): void
    {
        $author = (new Author())->setUri('https://www.archlinux.de/')->setName('Bob');

        $this->assertEquals('https://www.archlinux.de/', $author->getUri());
        $this->assertEquals('Bob', $author->getName());
    }
}
