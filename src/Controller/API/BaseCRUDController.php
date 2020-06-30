<?php

namespace App\Controller\API;

use App\Entity\UpdatableInterface;
use App\Util\MessageUtil;
use App\Util\Payload;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BaseCRUDController extends AbstractFOSRestController
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
     * @return BaseCRUDController
     */
    public function setSerializer(SerializerInterface $serializer): BaseCRUDController
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * @required
     *
     * @param LoggerInterface $logger
     *
     * @return BaseCRUDController
     */
    public function setLogger(LoggerInterface $logger): BaseCRUDController
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param Request $request
     * @param string $className
     * @param PaginatorInterface $paginator
     * @param array $serializationGroups
     *
     * @return Response
     */
    public function getItemsAction(Request $request, string $className, PaginatorInterface $paginator, array $serializationGroups)
    {
        $data = $this->decodeJsonContent($request);

        $paginatedResult = $this->getDoctrine()->getManager()->getRepository($className)->getPaginatedData($data, $paginator);

        return $this->getResponse($paginatedResult, MessageUtil::SUCCESS, Response::HTTP_OK, $serializationGroups);
    }

    /**
     * @param Request $request
     * @param string $formType
     * @param string $className
     * @param array $serializationGroups
     *
     * @return Response
     */
    public function createItemAction(Request $request, string $formType, string $className, array $serializationGroups)
    {
        $form = $this->createForm($formType);

        try {
            $object = $this->handleRequestWithJSONContent($request, $form);
        } catch (Throwable $e) {
            $this->logCriticalError('Error while handling form.', $e);

            return $this->getResponse(MessageUtil::VALIDATE_FORM, MessageUtil::ERROR, 400);
        }


        try {
            $this->save($object);
        } catch (Throwable $e) {
            $this->logCriticalError(sprintf('Error while saving: %s.', $className), $e);

            return $this->getResponse(MessageUtil::CAN_NOT_SAVE, MessageUtil::ERROR, 400);
        }

        return $this->getResponse($object, MessageUtil::SUCCESS, Response::HTTP_OK, $serializationGroups);
    }

    /**
     * @param Request $request
     * @param string $formType
     * @param string $className
     *
     * @return Response
     */
    public function updateItemAction(Request $request, string $formType, string $className)
    {
        $data = $this->decodeJsonContent($request);
        /** @var UpdatableInterface $object */
        $object = $this->getDoctrine()->getRepository($className)->find($data['id'] ?? -1);

        if (!$object) {
            return $this->getResponse(null,MessageUtil::CAN_NOT_FIND_OBJECT, 400);
        }

        $form = $this->createForm($formType, $object);

        /** @var UpdatableInterface $updatedObject */
        $updatedObject = $this->handleRequestWithJSONContent($request, $form);

        $object->update($updatedObject);

        $this->getDoctrine()->getManager()->flush();

        return $this->getResponse($object);
    }

    /**
     * @param Request $request
     * @param string $className
     *
     * @return Response
     */
    public function deleteItemAction(Request $request, string $className)
    {
        $data = $this->decodeJsonContent($request);

        $user = $this->getDoctrine()->getRepository($className)->find($data['id'] ?? -1);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();

        return $this->getResponse();
    }

    /**
     * @param mixed $rawData
     * @param string $status
     * @param int $httpCode
     * @param array $groups
     *
     * @return Response
     */
    protected function getResponse($rawData = '', string $status = MessageUtil::SUCCESS, int $httpCode = Response::HTTP_OK, array $groups = [])
    {
        $groups[] = 'Default';

        $context = SerializationContext::create()
            ->setGroups($groups)
            ->setSerializeNull(true);

        $payload = Payload::create($rawData, $status, $httpCode);
        $serializedData = $this->serializer->serialize($payload, 'json', $context);

        return new JsonResponse($serializedData, $payload->getHttpCode(), [], true);
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