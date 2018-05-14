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
           '_email' => 'mantas@amantas.lt',
           '_password' => '123',
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
            '_email' => 'mantas@mantas.lt',
            '_password' => '123456789',
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
