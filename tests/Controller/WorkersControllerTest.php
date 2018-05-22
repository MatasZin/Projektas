<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;

class WorkersControllerTest extends WebTestCase
{
    public function testWorkersPageAsUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'paprastas@paprastas.com',
            'PHP_AUTH_PW'   => 'paprastas',
        ));
        $client->request('GET', '/workers');

        $client->followRedirect();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/$/', $client->getRequest()->getPathInfo());
    }
    public function testWorkersPageAsAdmin()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin@admin.com',
            'PHP_AUTH_PW'   => 'admin',
        ));
        $client->request('GET', '/workers');

        //$client->followRedirect();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/workers$/', $client->getRequest()->getPathInfo());
    }

}
