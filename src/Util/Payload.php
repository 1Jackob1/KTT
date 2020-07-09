<?php

namespace App\Util;

use stdClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

class Payload
{
    /**
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $httpCode;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Payload constructor.
     *
     * @param mixed  $data
     * @param string $status
     * @param int    $httpCode
     * @param array  $errors
     */
    public function __construct($data, $status = MessageUtil::SUCCESS, $httpCode = Response::HTTP_OK, array $errors = [])
    {
        $this->data     = $data;
        $this->status   = $status;
        $this->httpCode = $httpCode;
        $this->errors   = $errors;
    }

    /**
     * @param mixed  $data
     * @param string $status
     * @param int    $httpCode
     * @param array  $errors
     *
     * @return Payload
     */
    public static function create($data = [], $status = MessageUtil::SUCCESS, $httpCode = Response::HTTP_OK, array $errors = [])
    {
        return new self($data, $status, $httpCode, $errors);
    }

    /**
     * @param string $status
     *
     * @return Payload
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $httpCode
     *
     * @return Payload
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param mixed $data
     *
     * @return Payload
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return ParameterBag
     */
    public function getDataBag()
    {
        return new ParameterBag(is_array($this->data) ? $this->data : []);
    }

    /**
     * Set errors.
     *
     * @param array $errors
     *
     * @return Payload
     */
    public function setErrors($errors)
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

    /**
     * @return mixed
     */
    public function getForResponse()
    {
        return [
            'data'   => $this->data ?: new stdClass(),
            'status' => $this->status,
            'errors' => $this->errors ?: new stdClass(),
        ];
    }
}
