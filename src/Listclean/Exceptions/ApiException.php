<?php

namespace Listclean\Exceptions;

use Exception;

class ApiException extends Exception
{
    /**
     * @var int|null
     */
    protected $statusCode;

    /**
     * @var array|null
     */
    protected $responseBody;

    public function __construct(string $message, ?int $statusCode = null, ?array $responseBody = null)
    {
        parent::__construct($message, $statusCode ?? 0);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): ?array
    {
        return $this->responseBody;
    }
}
