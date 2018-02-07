<?php

namespace Zshwag\AlipayBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GatewayControllerTest extends WebTestCase
{
    public function testNotify()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/notify');
    }

}
