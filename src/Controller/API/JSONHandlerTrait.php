<?php

namespace App\Controller\API;

use App\Exception\IncorrectDataException;
use Exception;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;

trait JSONHandlerTrait
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function decodeJsonContent(Request $request)
    {
        $content = trim($request->getContent());

        return json_decode($content, true);
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @param array $additionalData
     * @param bool $clearMissing
     *
     * @throws IncorrectDataException
     * @throws AlreadySubmittedException
     *
     * @return mixed
     */
    public function handleRequestWithJSONContent(Request $request, FormInterface $form, array $additionalData = [], $clearMissing = true)
    {
        $content = array_merge($this->decodeJsonContent($request), $additionalData);

        return $this->submitFormWithData($form, $content, $clearMissing);
    }

    /**
     * @param FormInterface $form
     * @param array $data
     * @param bool $clearMissing
     *
     * @throws IncorrectDataException
     * @throws AlreadySubmittedException
     *
     * @return mixed
     */
    public function submitFormWithData(FormInterface $form, array $data, $clearMissing = true)
    {
        $form->submit($data, $clearMissing);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->extractErrors($form);
        }

        return $form->getData();
    }

    /**
     * @param FormInterface $form
     *
     * @throws IncorrectDataException
     */
    public function extractErrors(FormInterface $form)
    {
        $errors     = [];

        /** @var FormError $error */
        foreach ($form->getErrors(true) as $error) {
            $name = $error->getOrigin()->getName();

            $name = $name !== $form->getName() ? $name : '_form';

            if (!array_key_exists($name, $errors)) {
                $errors[$name] = [];
            }

            /** @var ConstraintViolation $cause */
            $cause = $error->getCause();
            $errors[$name]['message'] = sprintf($error->getMessageTemplate(), $error->getMessageParameters());
            $errors[$name]['name'] = $cause->getConstraint()::getErrorName($cause->getCode());
        }

        foreach ($errors as &$fieldsErrors) {
            $fieldsErrors = array_unique($fieldsErrors);
        }

        unset($fieldsErrors);

        throw new IncorrectDataException($errors);
    }
}