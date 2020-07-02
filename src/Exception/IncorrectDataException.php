<?php


namespace App\Exception;


use App\Util\MessageUtil;
use Symfony\Component\HttpFoundation\Response;

class IncorrectDataException extends \Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @param array $errors
     */
    public function __construct(array $errors = [])
    {
        parent::__construct($this->getName(), $this->getHttpCode(), null);

        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return MessageUtil::VALIDATE_FORM;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return Response::HTTP_BAD_REQUEST;
    }

    /**
     * Set errors.
     *
     * @param array $errors
     *
     * @return IncorrectDataException
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
