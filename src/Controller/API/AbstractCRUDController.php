<?php

namespace App\Controller\API;

use App\Entity\UpdatableInterface;
use App\Exception\IncorrectDataException;
use App\Util\MessageUtil;
use App\Util\Payload;
use Doctrine\Common\Persistence\ObjectRepository;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class AbstractCRUDController extends AbstractFOSRestController
{
    use JSONHandlerTrait;

    /**
     * @var TransformedFinder
     */
    private $finder;

    /**
     * @param TransformedFinder $finder
     *
     * @return self
     */
    public function setFinder(TransformedFinder $finder): self
    {
        $this->finder = $finder;

        return $this;
    }

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
     * @return AbstractCRUDController
     */
    public function setSerializer(SerializerInterface $serializer): AbstractCRUDController
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * @required
     *
     * @param LoggerInterface $logger
     *
     * @return AbstractCRUDController
     */
    public function setLogger(LoggerInterface $logger): AbstractCRUDController
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param array $serializationGroups
     *
     * @return Response
     */
    public function getItemsAction(Request $request, PaginatorInterface $paginator, array $serializationGroups)
    {
        $data = $this->decodeJsonContent($request);

        $page = $data['_page'] ?? 1;
        $perPage = $data['_per_page'] ?? 5;

        unset($data['_page'], $data['_per_page']);

        $andX = new BoolQuery();
        $data['filter'] = $data['filter'] ?? [];
        foreach ($data['filter'] as $field => $value) {
            $match = new Query\Match();
            $match->setField($field, $value);
            $andX->addMust($match);
        }

        $result = $this->finder->createPaginatorAdapter($andX);
        $paginatedResult = $paginator->paginate($result, $page, $perPage);

        return $this->getResponse($paginatedResult, $serializationGroups, MessageUtil::SUCCESS, Response::HTTP_OK);
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
        } catch (IncorrectDataException $e) {
            return $this->getResponse('', [], MessageUtil::ERROR, $e->getHttpCode(), $e->getErrors());
        } catch (AlreadySubmittedException $e) {
            return $this->getResponse('', [], MessageUtil::ERROR, Response::HTTP_BAD_REQUEST, $e->getMessage());
        }


        try {
            $this->save($object);
        } catch (Throwable $e) {
            $this->logCriticalError(sprintf('Error while saving: %s.', $className), $e);

            return $this->getResponse('', [], 400, Response::HTTP_BAD_REQUEST, MessageUtil::CAN_NOT_SAVE);
        }

        return $this->getResponse($object, $serializationGroups, MessageUtil::SUCCESS, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param string $formType
     *
     * @return Response
     */
    public function updateItemAction(Request $request, string $formType)
    {
        $data = $this->decodeJsonContent($request);
        /** @var UpdatableInterface $object */
        $object = $this->getRepository()->find($data['id'] ?? -1);

        if (!$object) {
            return $this->getResponse('', [], MessageUtil::ERROR, Response::HTTP_BAD_REQUEST, MessageUtil::CAN_NOT_FIND_OBJECT);
        }

        $form = $this->createForm($formType);

        /** @var UpdatableInterface $updatedObject */
        try {
            $updatedObject = $this->handleRequestWithJSONContent($request, $form);
        } catch (IncorrectDataException $e) {
            return $this->getResponse('', [], MessageUtil::ERROR, $e->getHttpCode(), $e->getErrors());
        } catch (AlreadySubmittedException $e) {
            return $this->getResponse('', [], MessageUtil::ERROR, Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        $object->update($updatedObject);

        $this->getDoctrine()->getManager()->flush();

        return $this->getResponse($object);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteItemAction(Request $request)
    {
        $data = $this->decodeJsonContent($request);

        $user = $this->getRepository()->find($data['id'] ?? -1);
        if (!$user) {
            return $this->getResponse();
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();

        return $this->getResponse();
    }

    /**
     * @param mixed $rawData
     * @param array $groups
     *
     * @param string $status
     * @param int $httpCode
     * @param array $errors
     * @return Response
     */
    protected function getResponse($rawData = '', array $groups = [], string $status = MessageUtil::SUCCESS, int $httpCode = Response::HTTP_OK, $errors = null)
    {
        $groups[] = 'Default';

        $context = SerializationContext::create()
            ->setGroups($groups)
            ->setSerializeNull(true);

        if ($errors) {
            $errors = is_array($errors) ? $errors : [$errors];
        } else {
            $errors = [];
        }

        $payload = Payload::create($rawData, $status, $httpCode, $errors);
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

    /**
     * @return ObjectRepository
     */
    protected abstract function getRepository();
}