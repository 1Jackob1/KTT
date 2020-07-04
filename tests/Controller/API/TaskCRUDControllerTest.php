<?php

namespace App\Tests\Controller\API;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskCRUDControllerTest extends WebTestCase
{
    use TestHelperTrait;

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
}
