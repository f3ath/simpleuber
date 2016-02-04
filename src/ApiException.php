<?php
namespace F3\SimpleUber;

use RuntimeException;

class ApiException extends RuntimeException
{
    /**
     * @var string
     */
    private $errorCode;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var object
     */
    private $fields;

    /**
     * @var int
     */
    private $httpCode;

    /**
     * @param $httpCode
     * @param $error
     * @return ApiException
     */
    static public function create($httpCode, $error)
    {
        $exception = new self('Uber API exception', $httpCode);
        $exception->httpCode = $httpCode;
        if ($error) {
            $exception->errorCode = $error->code;
            $exception->errorMessage = $error->message;
            if (isset($error->fields)) {
                $exception->fields = $error->fields;
            }
        }
        return $exception;
    }

    /**
     * Get error code
     *
     * @see https://developer.uber.com/docs/api-reference#section-errors
     *
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get error message
     *
     * @see https://developer.uber.com/docs/api-reference#section-errors
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Get error fields
     * @see https://developer.uber.com/docs/api-reference#section-errors
     * @return object|null
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get HTTP code
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
}
