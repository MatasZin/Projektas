<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;

class CarsControllerTest extends WebTestCase
{
    public function testCarsPage()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'mantas@mantas.lt',
            'PHP_AUTH_PW'   => '123456789',
        ));
        $client->request('GET', '/cars');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        //$client->followRedirect();
        $this->assertRegExp('/\/cars$/', $client->getRequest()->getUri());
    }

    public function testAddNewCar()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'mantas@mantas.lt',
            'PHP_AUTH_PW' => '123456789',
        ));
        $crawler = $client->request('GET', '/cars');

        $form = $crawler->selectButton('Add car')->form();
        $form['car[licensePlate]'] = 'BNA 447';
        $client->submit($form);
        $client->followRedirect();
        //test response
        $this->assertEquals(200,
            $client->getResponse()->getStatusCode()
        );
    }
}
