<?php

namespace App\Tests\Controller\API;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserCRUDControllerTest extends WebTestCase
{
    use TestHelperTrait;

    /**
     * Test creating user without tasks
     */
    public function testCreateUserWithoutTasks()
    {
        $requestContent = '
        {
            "firstName": "FirstNameTest",
            "secondName": "SecondNameTest",
            "timezone": "Asia/Vladivostok"
        }
        ';

        $this->createObject('api/users', $requestContent, ['tasks', 'sessions']);
    }

    /**
     * Test creating user with task
     */
    public function testCreateUserWithTask()
    {
        $requestContent = '
        {
            "firstName": "FirstNameTest",
            "secondName": "SecondNameTest",
            "timezone": "Asia/Vladivostok",
            "tasks": [1]
        }
        ';

        $this->createObject('api/users', $requestContent, ['sessions']);
    }

    /**
     * Test failing on creating user
     */
    public function testFailCreatingUser()
    {
        $requestContent = '
        {
            "firstName": "",
            "secondName": "fff",
            "timezone": "Asia/Vladivostok",
            "tasks": [1]
        }
        ';

        $this->client->request('POST', 'api/users', [], [], [], $requestContent);

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertIsArray($decodedResponse);
        $this->assertArrayHasKey('errors', $decodedResponse);
        $this->assertEquals(1, count($decodedResponse['errors']));
        $this->assertArrayHasKey('firstName', $decodedResponse['errors']);
    }

    /**
     * Test deleting user
     */
    public function testDeleteUser()
    {
        $this->deleteObject('api/users', User::class);
    }

    /**
     * Test updating user
     */
    public function testUpdateUser()
    {
        $client = $this->client;

        $requestContent = '
        {
            "id": 5,
            "firstName": "Gooog",
            "secondName": "SecondNameTest",
            "timezone": "Asia/Vladivostok"
        }
        ';

        $client->request('PATCH', 'api/users', [], [], [], $requestContent);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
