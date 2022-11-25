<?php

namespace Rmk\JsonRpc;

/**
 * Error response class
 */
class ErrorResponse extends Response
{

    /**
     * @var int
     */
    protected int $code;

    /**
     * @var string
     */
    protected string $message;

    /**
     * @var mixed
     */
    protected mixed $data;

    /**
     * @param mixed|null $id
     * @param int $code
     * @param string $message
     * @param mixed|null $data
     */
    public function __construct(
        mixed  $id = null,
        int    $code = JsonRpcException::INTERNAL_ERROR,
        string $message = 'Internal Error',
        mixed  $data = null
    ) {
        parent::__construct($id);
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }


    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    public function respond(): array
    {
        return [
            'error' => [
                'code' => $this->getCode(),
                'message' => $this->getMessage(),
                'data' => $this->getData(),
            ]
        ];
    }
}