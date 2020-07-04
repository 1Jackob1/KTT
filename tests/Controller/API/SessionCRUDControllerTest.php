<?php

namespace App\Tests\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SessionCRUDControllerTest extends WebTestCase
{
    use TestHelperTrait;

    /**
     * Test starting session
     */
    public function testStartingSession()
    {
        $requestData = '{
            "user": 5,
            "task": 5,
            "timestamp": 155555
        }';

        $this->client->request('POST', 'api/sessions/start', [], [], [], $requestData);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test stopping session
     */
    public function testStoppingSession()
    {
        $requestData = '{
            "user": 5,
            "task": 5,
            "timestamp": 155555
        }';

        $this->client->request('POST', 'api/sessions/stop', [], [], [], $requestData);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}