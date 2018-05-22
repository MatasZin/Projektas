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
           '_email' => 'admin@admin.com',
           '_password' => 'adddddd',
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
            '_email' => 'admin@admin.com',
            '_password' => 'admin',
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
