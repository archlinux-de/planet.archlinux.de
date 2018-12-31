<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\PlanetController
 */
class PlanetControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
