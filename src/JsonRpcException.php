<?php

namespace Rmk\JsonRpc;

use Exception;

/**
 * JsonRpcException
 */
class JsonRpcException extends Exception
{

    public const PARSE_ERROR = -32700;

    public const INVALID_REQUEST = -32600;

    public const METHOD_NOT_FOUND = -32601;

    public const INVALID_PARAMS = -32602;

    public const INTERNAL_ERROR = -32603;

    protected mixed $id;

    protected int $httpStatus;

    /**
     * @param string $message
     * @param int $code
     * @param mixed $id
     * @param int $httpStatus
     */
    public function __construct(string $message, int $code = 0, $id = null, int $httpStatus = 200)
    {
        parent::__construct($message, $code);
        $this->httpStatus = $httpStatus;
        $this->id = $id;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getId()
    {
        return $this->id;
    }
}
