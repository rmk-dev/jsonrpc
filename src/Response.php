<?php

namespace Rmk\JsonRpc;

use JsonSerializable;

/**
 * Base response class
 *
 * The extending classes should define the different type of responses (success, error, notification)
 */
abstract class Response implements JsonSerializable, JsonRpcMessageInterface
{
    /**
     * The request id
     *
     * @var mixed
     */
    protected mixed $id;

    /**
     * Create new response obejct
     *
     * @param mixed $id The request id. Set null if the request is notification.
     */
    public function __construct(mixed $id)
    {
        $this->id = $id;
    }

    /**
     * The request id
     *
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * Return the called procedure's result
     *
     * @return array
     */
    abstract public function respond(): array;

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $response = $this->respond();
        $response['id'] = $this->getId();
        $response['jsonrpc'] = '2.0';

        return $response;
    }
}
