<?php

namespace App\Tests\Controller\API;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskCRUDControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * Test creating task without executors
     */
    public function testCreatingTaskWithoutExecutors()
    {
        $requestContent = '
        {
          "title": "third task",
          "description": "description",
          "estimate": 120,
          "priority": 10
        }
        ';

        $this->createObject('api/tasks', $requestContent, ['id', 'executors', 'sessions']);
    }

    /**
     * Test creating task without executors
     */
    public function testCreatingTaskWithExecutors()
    {
        $requestContent = '
        {
          "title": "third task",
          "description": "description",
          "estimate": 120,
          "priority": 10,
          "executors": [5, 6]
        }
        ';

        $this->createObject('api/tasks', $requestContent, ['id', 'sessions']);
    }

    /**
     * Test deleting user
     */
    public function testDeletingTask()
    {
        $this->deleteObject('api/tasks', Task::class);
    }

    /**
     * Test updating user
     */
    public function testUpdatingTask()
    {
        $requestContent = '
        {
          "id": 4,
          "title": "changed by test",
          "description": "description",
          "estimate": 120,
          "priority": 10,
          "executors": [5, 6]
        }
        ';

        $this->client->request('PATCH', 'api/tasks', [], [], [], $requestContent);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testFailCreatingOrUpdating()
    {
        $methods = [Request::METHOD_PATCH, Request::METHOD_POST];
        $requestContent = '
        {
          "id": 4,
          "title": "",
          "description": "description",
          "estimate": -1,
          "priority": 10,
          "executors": [5, 6]
        }
        ';

        foreach ($methods as $method) {
            $this->client->restart();
            $this->client->request($method, 'api/tasks', [], [], [], $requestContent);
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        }
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
     * @param string $uri
     * @param string $className
     */
    protected function deleteObject(string $uri, string $className)
    {
        $client = $this->client;

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $objects = $entityManager->getRepository($className)->findAll();

        $this->assertIsArray($objects);

        $objectId = array_pop($objects)->getId();

        $client->request('DELETE', $uri, [], [], [], "{ \"id\": {$objectId} }");

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @param string $uri
     * @param string $requestContent
     * @param array $additionalFieldsToCheck
     */
    protected function createObject(string $uri, string $requestContent, array $additionalFieldsToCheck)
    {
        $this->client->request('POST', $uri, [], [], [], $requestContent);
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $decodedResponse);

        $taskAsArray = $decodedResponse['data'];
        $this->assertIsArray($taskAsArray);

        $decodedRequestContent = json_decode($requestContent, true);

        foreach ($decodedRequestContent as $key => $value) {
            $this->assertArrayHasKey($key, $taskAsArray);
            if (is_array($taskAsArray[$key])) {
                foreach ($taskAsArray[$key] as $index => $userAsArray) {
                    $this->assertEquals($value[$index], $userAsArray['id']);
                }

                continue;
            }

            $this->assertEquals($value, $taskAsArray[$key]);
        }

        foreach ($additionalFieldsToCheck as $fieldName) {
            $this->assertArrayHasKey($fieldName, $taskAsArray);

        }
    }
}
