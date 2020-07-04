<?php

namespace App\Tests\Controller\API;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

trait TestHelperTrait
{
    /**
     * @var KernelBrowser
     */
    protected $client;

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
            $key = Container::underscore($key);

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