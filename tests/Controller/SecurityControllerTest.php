<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPage()
    {
        $client = static::createClient();

        $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginInvalid()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form(array(
           '_email' => 'Matas@gmail.com',
           '_password' => 'mata123',
        ));

        // submit the form
        $client->submit($form);
        $client->followRedirect();
        $this->assertRegExp('/\/login$/', $client->getRequest()->getUri());
        //test response
        $this->assertEquals(200,
            $client->getResponse()->getStatusCode()
        );
    }
    public function testLoginValid()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form(array(
            '_email' => 'Matas@gmail.com',
            '_password' => 'matas123',
        ));

        // submit the form
        $client->submit($form);
        $client->followRedirect();
        $this->assertRegExp('/\/$/', $client->getRequest()->getUri());
        //test response
        $this->assertEquals(200,
            $client->getResponse()->getStatusCode()
        );
    }
}
