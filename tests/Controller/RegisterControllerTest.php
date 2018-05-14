<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;

class RegisterControllerTest extends WebTestCase
{
    public function testRegistrationPage()
    {
        $client = static::createClient();

        $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRegistrationForm()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('Register')->form();

        // set some values
        $form['user[email]'] = 'Lucas@hotmail.com';
        $form['user[name]'] = 'Lucas';
        $form['user[second_name]'] = 'asd';
        $form['user[password][first]'] = '123456';
        $form['user[password][second]'] = '123456';

        // submit the form
        $client->submit($form);
        $client->followRedirect();
        //test response
        $this->assertEquals(200,
            $client->getResponse()->getStatusCode()
        );
    }
}
