<?php

namespace App\Tests\Controller\API;

use App\Entity\User;
use stdClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserCRUDControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * Test creating user without tasks
     */
    public function testCreateUserWithoutTasks()
    {
        $client = $this->client;

        $requestContent = '
        {
            "firstName": "FirstNameTest",
            "secondName": "SecondNameTest",
            "timezone": "Asia/Vladivostok"
        }
        ';

        $responseContent = $this->assertResponseAndGetContent($client, $requestContent);

        $data = $responseContent['data'];

        $this->assertCoreFields($data);
    }

    /**
     * Test creating user with task
     */
    public function testCreateUserWithTask()
    {
        $client = $this->client;

        $requestContent = '
        {
            "firstName": "FirstNameTest",
            "secondName": "SecondNameTest",
            "timezone": "Asia/Vladivostok",
            "tasks": [1]
        }
        ';

        $responseContent = $this->assertResponseAndGetContent($client, $requestContent);

        $data = $responseContent['data'];

        $this->assertCoreFields($data);

        $this->assertIsArray($data['tasks']);

        foreach ($data['tasks'] as $task) {
            $this->assertIsArray($task);
        }

        $this->assertEquals(0, count($data['sessions']));
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

        $this->client->request('POST','api/users', [], [], [], $requestContent);

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
        $client = $this->client;

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $users = $entityManager->getRepository(User::class)->findAll();

        $this->assertIsArray($users);

        $userId = array_pop($users)->getId();

        $client->request('DELETE', 'api/users', [], [], [], "{ \"id\": {$userId} }");

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
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

        $this->assertResponseAndGetContent($client, $requestContent, 'PATCH');
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        self::ensureKernelShutdown();

        $this->client = self::createClient();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        $this->client->restart();
    }

    /**
     * @param $data
     */
    private function assertCoreFields($data): void
    {
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('first_name', $data);
        $this->assertArrayHasKey('second_name', $data);
        $this->assertArrayHasKey('last_name', $data);
        $this->assertArrayHasKey('timezone', $data);
        $this->assertArrayHasKey('tasks', $data);
        $this->assertArrayHasKey('sessions', $data);
    }

    /**
     * @param KernelBrowser $client
     * @param string $requestContent
     * @param string $method
     *
     * @return stdClass
     */
    private function assertResponseAndGetContent(KernelBrowser $client, string $requestContent, string $method = 'POST')
    {
        $client->request($method, 'api/users', [], [], [], $requestContent);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $responseContent = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey('data', $responseContent);
        $this->assertIsArray($responseContent['data']);

        return $responseContent;
    }
}
