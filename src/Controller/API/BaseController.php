<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Form\UserFormType;
use App\Util\MessageUtil;
use App\Util\Payload;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BaseController extends AbstractFOSRestController
{
    use JSONHandlerTrait;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @required
     *
     * @param SerializerInterface $serializer
     *
     * @return BaseController
     */
    public function setSerializer(SerializerInterface $serializer): BaseController
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * @required
     *
     * @param LoggerInterface $logger
     *
     * @return BaseController
     */
    public function setLogger(LoggerInterface $logger): BaseController
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param mixed  $rawData
     * @param string $status
     * @param int    $httpCode
     * @param array  $groups
     *
     * @return Response
     */
    protected function getResponse($rawData = '', string $status = MessageUtil::SUCCESS, int $httpCode = Response::HTTP_OK, array $groups = [])
    {
        $groups[] = 'Default';

        $context = SerializationContext::create()
            ->setGroups($groups)
            ->setSerializeNull(true);
        $serializedData = $this->serializer->serialize($rawData, 'json', $context);

        $payload = Payload::create($serializedData, $status, $httpCode);

        return new JsonResponse($payload->getForResponse(), $payload->getHttpCode());
    }


    /**
     * @param string $description
     * @param Throwable $e
     */
    protected function logCriticalError(string $description, $e): void
    {
        $this->logger->critical($description, [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ]);
    }

    /**
     * @param $object
     */
    protected function save($object)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($object);
        $manager->flush();
    }
}